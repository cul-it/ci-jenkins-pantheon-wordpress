<?php
/**
 * Class SmackXMLImporter
 */
class SmackNewXMLImporter {


	/**
	 * @var domXPath
	 */
	public $xpath;

	/**
	 * @var mixed
	 */
	public $xdoc;

	/**
	 * SmackXMLImporter constructor.
	 */
	function __construct() {
		$this->xdoc  = new DOMDocument();
		$this->xpath = new domXPath( $this->xdoc );
	}

	/**
	 * get XML Tree Structure
	 * @param $node
	 */
	function treeNode( $node ) {
		if ( $node->nodeName != '#text' ) {
			if ( $node->childNodes->length != 1 && $node->nodeName != '#cdata-section' ) { ?>
				<ul>
				<li id="data"> <label style="color:#8B008B" id='labeltext'>
					<b><?php echo '+&lt;' . $node->nodeName . '&gt;'; ?> </b></label><div id='collapse'>
			<?php } //echo '<pre>'; print_r($node->childNodes);
			//get all childNodes
			if ( $node->hasChildNodes() ) {
				foreach ( $node->childNodes as $child ) {
					treeNode( $child );
				}
				//get all attributes
				if ( $node->hasAttributes() ) {
					for ( $i = 0; $i <= $node->attributes->length; ++ $i ) {
						$attr_nodes = $node->attributes->item( $i );
						if ( $attr_nodes->nodeName && $attr_nodes->nodeValue ) {
							$attrs[ $node->nodeName ][ $attr_nodes->nodeName ] = $attr_nodes->nodeValue;
						}
					}
				}
				//get all nodeName and nodeValue
				if ( $node->nodeValue || $node->nodeValue == 0 ) {
					if ( $node->childNodes->length == 1 ) {
						?>
						<ul>
							<li>
								<label style="color:#8B008B">
									<b><?php echo '&lt;' . $node->nodeName . '&gt;'; ?> </b></label>
								<!--<span> <b><?php echo 'NODEPATH:' . $node->getNodePath(); ?></b></span> -->
								<span
									title='<?php echo $node->getNodePath(); ?>'> <?php echo $node->nodeValue; ?></span>
								<label style="color:#8B008B">
									<b><?php echo '&lt;/' . $node->nodeName . '&gt;'; ?> </b></label>
							</li>
						</ul>
					<?php }
				}
			}
			if ( $node->childNodes->length != 1 && $node->nodeName != '#cdata-section' ) { ?>
				</div><label style="color:#8B008B"> <b><?php echo '&lt;/' . $node->nodeName . '&gt;'; ?></b></label>
				</li>
				</ul>

			<?php }
		}
	}

	/**
	 * get node length
	 * @param $node
	 * @return mixed
	 */
	function get_nodes_length($xml,$node ) {
		$nodes = $xml->getElementsByTagName( $node );
		return $nodes->length;
	}


	/**
	 * get particular node tree
	 * @param $node
	 * @param $row
	 * @return mixed
	 */
	function get_xmltree( $xml,$node, $row ) {
		while ( is_object( $xmldoc = $xml->getElementsByTagName( $node )->item( $row ) ) ) {
			return treeNode( $xmldoc );
			die;
		}
	}

	/**
	 * @param $xml
	 * @param $query
	 *
	 * @return string
	 */
	function parse_element($xml,$query){
		$xpath = new DOMXPath($xml);
		$entries = $xpath->query($query);
		$content = $entries->item(0)->textContent;
		return $content;
	}

	/**
	 * @param $node
	 * This is specific element array
	 * @return mixed
	 */
	//$node = $xml->getElementsByTagName('item')->item(2);
	//$mm = get_xmlelement($node);
	function get_xmlelement($node){
	    if ( $node->nodeName != '#text' ) {
	        if ( $node->childNodes->length != 1 && $node->nodeName != '#cdata-section' ) {
	            if ( $node->hasChildNodes() ) {
	                foreach ( $node->childNodes as $child ) {
	                    get_xmlelement( $child );
	                    if($child->nodeName != '#text')
	                        $xmlelement[$child->nodeName] = $child->nodeValue;
	                }
	            }
	        }
	    }
	    return $xmlelement;
	}
}

?>
