<label class="control-label cm-required" for="elm_product_full_descr">{__("full_description")}:</label>
<div class="controls">
    {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id="full_description" name="update_all_vendors[full_description]"}
    <textarea id="elm_product_full_descr" name="product_data[full_description]" cols="55" rows="8" class="cm-wysiwyg input-large">{$product_data.full_description}</textarea>

    {if $view_uri}
        {include
        file="buttons/button.tpl"
        but_href="customization.update_mode?type=live_editor&status=enable&frontend_url={$view_uri|urlencode}{if "ULTIMATE"|fn_allowed_for}&switch_company_id={$product_data.company_id}{/if}"
        but_text=__("edit_content_on_site")
        but_role="action"
        but_meta="btn-small btn-live-edit cm-post"
        but_target="_blank"}
    {/if}
</div>