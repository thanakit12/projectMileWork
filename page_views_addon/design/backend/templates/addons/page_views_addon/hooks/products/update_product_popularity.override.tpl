{* Add View Bought delete row in product detail page after popularity field for core *}
<div class="control-group">
    <label class="control-label" for="elm_product_popularity">{__("popularity")}:</label>
    <div class="controls">
        <input type="text" {if $disable_edit_popularity}disabled="disabled"{/if} name="product_data[popularity]" id="elm_product_popularity" size="55" value="{$product_data.popularity|default:0}" class="input-long" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="elm_product_view">{__("viewed")}:</label>
    <div class="controls">
        <input type="text"  disabled="disabled" name="product_data[viewed]" id="elm_product_viewed" size="55" value="{$product_data.viewed|default:0}" class="input-long" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="elm_product_view">{__("added_to_cart")}:</label>
    <div class="controls">
        <input type="text"  disabled="disabled" name="product_data[added_to_cart]" id="elm_product_added_to_cart" size="55" value="{$product_data.added_to_cart|default:0}" class="input-long" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="elm_product_view">{__("deleted_from_cart")}:</label>
    <div class="controls">
        <input type="text" disabled="disabled" name="product_data[deleted_from_cart]" id="elm_product_deleted_from_cart" size="55" value="{$product_data.deleted_from_cart|default:0}" class="input-long" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="elm_product_view">{__("bought")}:</label>
    <div class="controls">
        <input type="text" disabled="disabled"  name="product_data[bought]" id="elm_product_bought" size="55" value="{$product_data.bought|default:0}" class="input-long" />
    </div>
</div>

