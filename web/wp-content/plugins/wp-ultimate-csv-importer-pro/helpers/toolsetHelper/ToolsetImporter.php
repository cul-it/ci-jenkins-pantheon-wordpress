<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ToolsetImporter {

    public function format($Parent_Group,$postTypeValue) {

        if(!empty($Parent_Group)) {

            return $this->separateElements($Parent_Group,$postTypeValue);

        }else {
            //print_r("Parent Group Name not mapped");
            return 0;
            //TODO Need to implement when there is no relationship slug
        }
    }

    public function separateElements($parentGroupName,$postTypeValue) {
        global $wpdb;

        $result = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."posts WHERE post_name ='{$parentGroupName}' and post_type='{$postTypeValue}'");
        if (!empty($result[0]->id)) {
            $result = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id ='{$result[0]->id}' and meta_key ='_wp_types_group_fields'");
            $elementString=$result[0]->meta_value;
            $elementArray=$this->explodeFunction(',',$elementString);
            return $elementArray;
        } else {
            //Parent group post not found
            print_r("Parent Group Name not found");
            return 0;
        }
    }

    public function getRelationshipId($groupName)
    {
        global $wpdb;
        $relation_id = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."toolset_relationships WHERE slug = '{$groupName}'");
        $relation_id=$relation_id[0]->id;
        return $relation_id;
    }

    public function insertPost($groupName,$title)
    {
        global $wpdb;
        $post_id = wp_insert_post( array(
                    'post_status' => 'publish',
                    'post_title' => $title,
                    'post_type' => $groupName
                    ));
        return $post_id;
    }

    public function insertRelationship($groupRelationId,$postId,$childPostId)
    {
        global $wpdb;
        $wpdb->insert($wpdb->prefix.'toolset_associations', array(
                    'relationship_id'   => $groupRelationId,
                    'parent_id'         => $postId,
                    'child_id'          => $childPostId,
                    'intermediary_id'   => '0'
                    ));
    }

    public function getRepeatableMetaValue($value)
    {
        global $wpdb;
        $meta = $wpdb->get_results("SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id = {$value} and meta_key = '_wp_types_group_fields'");
        return $meta[0]->meta_value;
    }

    public function getRepeatableName($value)
    {
        global $wpdb;
        $meta = $wpdb->get_results("SELECT post_name FROM ".$wpdb->prefix."posts WHERE id = {$value}");

        return $meta[0]->post_name;
    }

    public function explodeFunction($symbol,$value)
    {
        $explode = explode($symbol,$value);

        foreach($explode as $key => $value)
        {
            if(is_null($value) || $value == '')
                unset($explode[$key]);
        }
        return $explode;
    }

    public function getMetaKeys($postId,$postType)
    {
        global $wpdb;
        if ($postType == 'users') {
            return $wpdb->get_results("SELECT umeta_id,meta_key FROM ".$wpdb->prefix."usermeta WHERE user_id = {$postId}",ARRAY_A);
        }else{
            return $wpdb->get_results("SELECT meta_id,meta_key FROM ".$wpdb->prefix."postmeta WHERE post_id = {$postId}",ARRAY_A);
        }

    }
    public function checkTermKeys($postId,$postType)
    {
        global $wpdb;
        $result=$wpdb->get_results("SELECT term_id FROM ".$wpdb->prefix."term_taxonomy WHERE taxonomy = '{$postType}' ",ARRAY_A);
        if (!empty($result)){
            return 1;
        }
    }
    public function getTermKeys($postId,$postType)
    {
        global $wpdb;
        return $wpdb->get_results("SELECT meta_id,meta_key FROM ".$wpdb->prefix."termmeta WHERE term_id = {$postId}",ARRAY_A);
    }

    public function deleteMetaKeys($metaId,$postType)
    {
        global $wpdb;
        if ($postType == 'users') {
            $wpdb->delete( $wpdb->prefix.'usermeta', array( 'umeta_id' => $metaId));
        }else{
            $wpdb->delete( $wpdb->prefix.'postmeta', array( 'meta_id' => $metaId));
        }

    }
    public function deleteTermMetaKeys($postId,$postType)
    {

        global $wpdb;       
        $wpdb->delete( $wpdb->prefix.'termmeta', array( 'term_id' => $postId));

    }

    public function getMetaKeyId($postId,$metaKeyname,$postType)
    {
        global $wpdb;
        if ($postType == 'users') {
            return $wpdb->get_results("SELECT umeta_id,meta_value FROM ".$wpdb->prefix."usermeta WHERE user_id = {$postId} and meta_key = '{$metaKeyname}'",ARRAY_A);
        }else{
            return $wpdb->get_results("SELECT meta_id,meta_value FROM ".$wpdb->prefix."postmeta WHERE post_id = {$postId} and meta_key = '{$metaKeyname}'",ARRAY_A);
        }
    }
    public function findRelationship($postId)
    {
        global $wpdb;
        return $wpdb->get_results("SELECT id,child_id FROM ".$wpdb->prefix."toolset_associations WHERE parent_id = {$postId}",ARRAY_A);
    }
}
