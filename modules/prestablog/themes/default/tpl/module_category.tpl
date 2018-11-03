{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}
<!-- Module Presta Blog -->
{if $prestablog_categorie_obj->image_presente && $prestablog_config.prestablog_view_cat_img}
<img class="prestablog_cat_img" src="{$prestablog_theme_dir}up-img/c/full_{$prestablog_categorie_obj->id}.jpg?{$md5pic}" alt="{$prestablog_categorie_obj->title}" />
{/if}
{if $prestablog_categorie_obj->image_presente && $prestablog_config.prestablog_view_cat_thumb}
<img src="{$prestablog_theme_dir}up-img/c/thumb_{$prestablog_categorie_obj->id}.jpg?{$md5pic}" alt="{$prestablog_categorie_obj->title}" class="prestablog_thumb_cat"/>
{/if}
{if isset($prestablog_categorie_obj->description) && $prestablog_config.prestablog_view_cat_desc}
{$prestablog_categorie_obj->description}
{/if}
<div class="clearfix"></div>
<!-- /Module Presta Blog -->