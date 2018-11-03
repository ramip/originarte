{if !empty($HOOK_LEFT_COLUMN)&& empty($HOOK_RIGHT_COLUMN)}
  {if $page_name == 'index'} {* control añadido para mostrar columnLeft solo en home *}
	{assign var='LAYOUT_COLUMN_SPANS' value=array(3,9,0) scope='global'} {* original *}
  {else}
	{assign var='LAYOUT_COLUMN_SPANS' value=array(0,12,0) scope='global'} {* add for show one column  *}
  {/if}
{elseif empty($HOOK_LEFT_COLUMN)&& !empty($HOOK_RIGHT_COLUMN)}
{assign var='LAYOUT_COLUMN_SPANS' value=array(0,9,3) scope='global'}
{elseif empty($HOOK_LEFT_COLUMN)&&empty($HOOK_RIGHT_COLUMN)}
{assign var='LAYOUT_COLUMN_SPANS' value=array(0,12,0) scope='global'}
{else}
{assign var='LAYOUT_COLUMN_SPANS' value=array(3,6,3) scope='global'}
{/if}


{if $LAYOUT_COLUMN_SPANS[0]}
<!-- Left -->
<section id="left_column" class="column span{$LAYOUT_COLUMN_SPANS[0]}">
	{$HOOK_LEFT_COLUMN}
</section>
{/if}
<!-- Center -->
<section id="center_column" class="span{$LAYOUT_COLUMN_SPANS[1]}">
