<div class="facetwp-header">
    <span class="facetwp-logo" title="FacetWP">&nbsp;</span>
    <span class="facetwp-version">v<?php echo FWPCL_VERSION; ?></span>
    <span class="facetwp-header-nav">
        <a class="facetwp-tab" rel="rulesets"><?php _e( 'Rulesets', 'facetwp-conditional-logic' ); ?></a>
        <a class="facetwp-tab" rel="settings"><?php _e( 'Settings', 'facetwp-conditional-logic' ); ?></a>
    </span>
</div>

<div class="wrap">
    <div class="facetwp-response"></div>

    <div class="facetwp-region facetwp-region-rulesets">
        <div class="flexbox">
            <a class="button add-ruleset"><?php _e( 'Add Ruleset', 'facetwp-conditional-logic' ); ?></a>
            <a class="button facetwp-save" style="margin-left:10px"><?php _e( 'Save Changes', 'facetwp-conditional-logic' ); ?></a>
            <span class="fwpcl-response"></span>
        </div>

        <div class="facetwp-content-wrap"></div>
    </div>

    <div class="facetwp-region facetwp-region-settings">
        <div class="facetwp-content-wrap">
            <p class="description"><?php _e( 'To export, copy the code below.', 'facetwp-conditional-logic' ); ?></p>
            <input type="text" class="export-code" readonly="readonly" />
            <p class="description" style="margin-top:20px"><?php _e( 'To import, paste code into the field below.', 'facetwp-conditional-logic' ); ?></p>
            <textarea class="import-code"></textarea>
            <p class="description" style="color:red">
                <?php _e( 'Importing will replace any existing rulesets.', 'facetwp-conditional-logic' ); ?>
            </p>
            <input type="button" class="button fwpcl-import" value="Process Import" />
            <span class="fwpcl-import-response"></span>
        </div>
    </div>

    <!-- [Begin] Clone HTML -->

    <div class="clone hidden">
        <div class="clone-ruleset">
            <div class="ruleset">
                <table class="header-bar">
                    <tr>
                        <td class="toggle"><span class="dashicons dashicons-menu"></span></td>
                        <td class="title"><span class="ruleset-label" contenteditable="true"><?php _e( 'Change me', 'facetwp-conditional-logic' ); ?></span></td>
                        <td class="delete"><span class="dashicons dashicons-no-alt"></span></td>
                    </tr>
                </table>
                <table class="logic-row">
                    <tr>
                        <td class="conditions-col" style="width:60%">
                            <div class="td-label"><?php _e( 'Conditions', 'facetwp-conditional-logic' ); ?></div>
                            <div class="condition-wrap"></div>
                            <button class="button condition-and"><?php _e( 'Add Condition', 'facetwp-conditional-logic' ); ?></button>
                        </td>
                        <td class="actions-col" style="width:40%">
                            <div class="td-label"><?php _e( 'Actions', 'facetwp-conditional-logic' ); ?></div>
                            <div class="action-wrap"></div>
                            <table class="action-else-table">
                                <tr>
                                    <td class="drop"></td>
                                    <td class="type"><?php _e( 'ELSE', 'facetwp-conditional-logic' ); ?></td>
                                    <td class="logic">
                                        <select class="action-else">
                                            <option value="flip"><?php _e( 'Do the opposite', 'facetwp-conditional-logic' ); ?></option>
                                            <option value="skip"><?php _e( 'Do nothing', 'facetwp-conditional-logic' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <button class="button action-and"><?php _e( 'Add Action', 'facetwp-conditional-logic' ); ?></button>
                        </td>
                    <tr>
                </table>
            </div>
        </div>

        <div class="clone-condition">
            <table class="condition">
                <tr>
                    <td class="spacer"></td>
                    <td class="drop">
                        <span class="dashicons dashicons-no-alt condition-drop"></span>
                    </td>
                    <td class="type"><?php _e( 'IF', 'facetwp-conditional-logic' ); ?></td>
                    <td class="logic">
                        <select class="condition-object">
                            <optgroup label="Basic">
                                <option value="facets-empty"><?php _e( 'No facets in use', 'facetwp-conditional-logic' ); ?></option>
                                <option value="facets-not-empty"><?php _e( 'Some facets in use', 'facetwp-conditional-logic' ); ?></option>
                                <option value="uri"><?php _e( 'Page URI', 'facetwp-conditional-logic' ); ?></option>
                                <option value="total-rows"><?php _e( 'Result count', 'facetwp-conditional-logic' ); ?></option>
                            </optgroup>
                            <optgroup label="Facet Value">
<?php foreach ( $this->facets as $facet ) : ?>
                                <option value="facet-<?php echo $facet['name']; ?>"><?php _e( 'Facet', 'facetwp-conditional-logic' ); ?>: <?php echo $facet['label']; ?></option>
<?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Template">
<?php foreach ( $this->templates as $template ) : ?>
                                <option value="template-<?php echo $template['name']; ?>"><?php _e( 'Template', 'facetwp-conditional-logic' ); ?>: <?php echo $template['label']; ?></option>
<?php endforeach; ?>
                            </optgroup>
                        </select>
                        <select class="condition-compare">
                            <option value="is"><?php _e( 'is', 'facetwp-conditional-logic' ); ?></option>
                            <option value="not"><?php _e( 'is not', 'facetwp-conditional-logic' ); ?></option>
                        </select>
                        <input type="text" class="condition-value" placeholder="<?php _e( 'enter values', 'facetwp-conditional-logic' ); ?>" title="comma-separate multiple values"></input>
                    </td>
                    <td class="btn">
                        <button class="button condition-or"><?php _e( 'OR', 'facetwp-conditional-logic' ); ?></button>
                    </td>
                </tr>
            </table>
        </div>

        <div class="clone-action">
            <table class="action">
                <tr>
                    <td class="drop">
                        <span class="dashicons dashicons-no-alt action-drop"></span>
                    </td>
                    <td class="type"><?php _e( 'AND', 'facetwp-conditional-logic' ); ?></td>
                    <td class="logic">
                        <select class="action-toggle">
                            <option value="show"><?php _e( 'Show', 'facetwp-conditional-logic' ); ?></option>
                            <option value="hide"><?php _e( 'Hide', 'facetwp-conditional-logic' ); ?></option>
                        </select>
                        <select class="action-object">
                            <option value="template"><?php _e( 'Template', 'facetwp-conditional-logic' ); ?></option>
                            <option value="facets"><?php _e( 'All Facets', 'facetwp-conditional-logic' ); ?></option>
                            <optgroup label="Facets">
<?php foreach ( $this->facets as $facet ) : ?>
                                <option value="facet-<?php echo $facet['name']; ?>"><?php _e( 'Facet', 'facetwp-conditional-logic' ); ?>: <?php echo $facet['label']; ?></option>
<?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Custom">
                                <option value="custom"><?php _e( 'Selector', 'facetwp-conditional-logic' ); ?></option>
                            </optgroup>
                        </select>
                        <textarea class="action-selector hidden" placeholder="$('.facetwp-facet-categories')"></textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- [End] Clone HTML -->

</div>
