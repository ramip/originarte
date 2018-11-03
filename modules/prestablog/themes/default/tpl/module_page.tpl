{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}
<!-- Module Presta Blog START PAGE -->

{capture name=path}<a href="{PrestaBlogUrl}" >{l s='Blog' mod='prestablog'}</a>{if $SecteurName}&nbsp;>&nbsp;{$SecteurName}{/if}{/capture}

{if isset($tpl_menu_cat) && $tpl_menu_cat}{$tpl_menu_cat}{/if}

{if isset($tpl_unique) && $tpl_unique}{$tpl_unique}{/if}
{if isset($tpl_comment) && $tpl_comment}{$tpl_comment}{/if}

{if isset($tpl_slide) && $tpl_slide}{$tpl_slide}{/if}
{if isset($tpl_cat) && $tpl_cat}{$tpl_cat}{/if}
{if isset($tpl_all) && $tpl_all}{$tpl_all}{/if}

<!-- /Module Presta Blog END PAGE -->
