{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}
<!-- Module Presta Blog -->
{if isset($News)}
	<a name="article"></a>
	<h1>{$News->title}</h1>
	<p class="info_blog">{l s='Published :' mod='prestablog'} 
	{dateFormat date=$News->date full=1}
	{if sizeof($News->categories)}
		<br />
		{l s='Categories :' mod='prestablog'} 
		{foreach from=$News->categories item=categorie key=key name=current}<a href="{PrestaBlogUrl c=$key titre=$categorie.link_rewrite}">{$categorie.title}</a>
		{if $prestablog_config.prestablog_uniqnews_rss}<sup><a target="_blank" href="{PrestaBlogUrl rss=$key}"><img src="{$prestablog_theme_dir}/img/rss.png" alt="Rss feed" align="absmiddle" /></a></sup>{/if}
		{if !$smarty.foreach.current.last},{/if}
		{/foreach}
	{/if}
	</p>
	{if isset($News_Image) && $prestablog_config.prestablog_view_news_img}<img src="{$prestablog_theme_dir}up-img/thumb_{$News->id}.jpg?{$md5pic}" class="news" alt="{$News->title}"/>{/if}
	<div id="prestablogfont">{$News->content}</div>
	<div class="clear"></div>
	{if (sizeof($News->products_liaison))}
    <div id="blog_product_linked">
		<h3>{l s='Products link' mod='prestablog'}</h3>
		{foreach from=$News->products_liaison item=article key=key name=current}
		<div class="productslinks">
				<a href="{$article.link}">
					{$article.thumb}
                    <br/>
					{$article.name}
				</a>
        </div>
		{/foreach}
    </div>
	{/if}
	{if $prestablog_config.prestablog_socials_actif}
		<h3>{l s='Share this content' mod='prestablog'}</h3>
		<ul class="rrssb-buttons clearfix">
        <li class="facebook">
            <a href="https://www.facebook.com/sharer/sharer.php?u={$prestablog_current_url}" class="popup">
                <span class="icon">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                        <path d="M27.825,4.783c0-2.427-2.182-4.608-4.608-4.608H4.783c-2.422,0-4.608,2.182-4.608,4.608v18.434
                            c0,2.427,2.181,4.608,4.608,4.608H14V17.379h-3.379v-4.608H14v-1.795c0-3.089,2.335-5.885,5.192-5.885h3.718v4.608h-3.726
                            c-0.408,0-0.884,0.492-0.884,1.236v1.836h4.609v4.608h-4.609v10.446h4.916c2.422,0,4.608-2.188,4.608-4.608V4.783z"/>
                    </svg>
                </span>
                <span class="text">{l s='Facebook' mod='prestablog'}</span>
            </a>
        </li>
        <li class="twitter">
			<!--<a href="http://twitter.com/home?status={$News->title}%20{$prestablog_current_url}" class="popup">-->
			 <div id="custom-tweet-button">
				<a href="http://twitter.com/share?url=http%3A%2F%2Fdev.twitter.com%2Fpages%2Ftweet-button&text=Originarte%2Diseño%2e%Impresión" target="_blank">Twitter</a>
			 </div>
        </li>
        <li class="googleplus">
            <a href="https://plus.google.com/share?url={$News->title}%20{$prestablog_current_url}" class="popup">
                <span class="icon">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                        <g>
                            <g>
                                <path d="M14.703,15.854l-1.219-0.948c-0.372-0.308-0.88-0.715-0.88-1.459c0-0.748,0.508-1.223,0.95-1.663
                                    c1.42-1.119,2.839-2.309,2.839-4.817c0-2.58-1.621-3.937-2.399-4.581h2.097l2.202-1.383h-6.67c-1.83,0-4.467,0.433-6.398,2.027
                                    C3.768,4.287,3.059,6.018,3.059,7.576c0,2.634,2.022,5.328,5.604,5.328c0.339,0,0.71-0.033,1.083-0.068
                                    c-0.167,0.408-0.336,0.748-0.336,1.324c0,1.04,0.551,1.685,1.011,2.297c-1.524,0.104-4.37,0.273-6.467,1.562
                                    c-1.998,1.188-2.605,2.916-2.605,4.137c0,2.512,2.358,4.84,7.289,4.84c5.822,0,8.904-3.223,8.904-6.41
                                    c0.008-2.327-1.359-3.489-2.829-4.731H14.703z M10.269,11.951c-2.912,0-4.231-3.765-4.231-6.037c0-0.884,0.168-1.797,0.744-2.511
                                    c0.543-0.679,1.489-1.12,2.372-1.12c2.807,0,4.256,3.798,4.256,6.242c0,0.612-0.067,1.694-0.845,2.478
                                    c-0.537,0.55-1.438,0.948-2.295,0.951V11.951z M10.302,25.609c-3.621,0-5.957-1.732-5.957-4.142c0-2.408,2.165-3.223,2.911-3.492
                                    c1.421-0.479,3.25-0.545,3.555-0.545c0.338,0,0.52,0,0.766,0.034c2.574,1.838,3.706,2.757,3.706,4.479
                                    c-0.002,2.073-1.736,3.665-4.982,3.649L10.302,25.609z"/>
                                <polygon points="23.254,11.89 23.254,8.521 21.569,8.521 21.569,11.89 18.202,11.89 18.202,13.604 21.569,13.604 21.569,17.004
                                    23.254,17.004 23.254,13.604 26.653,13.604 26.653,11.89      "/>
                            </g>
                        </g>
                    </svg>
                </span>
                <span class="text">{l s='Google+' mod='prestablog'}</span>
            </a>
        </li>
        <li class="linkedin">
            <a href="http://www.linkedin.com/shareArticle?mini=true&url={$prestablog_current_url}&title={$News->title}&summary={$News->title}" class="popup">
                <span class="icon">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" height="28px" viewBox="0 0 28 28" enable-background="new 0 0 28 28" xml:space="preserve">
                        <path d="M25.424,15.887v8.447h-4.896v-7.882c0-1.979-0.709-3.331-2.48-3.331c-1.354,0-2.158,0.911-2.514,1.803
                            c-0.129,0.315-0.162,0.753-0.162,1.194v8.216h-4.899c0,0,0.066-13.349,0-14.731h4.899v2.088c-0.01,0.016-0.023,0.032-0.033,0.048
                            h0.033V11.69c0.65-1.002,1.812-2.435,4.414-2.435C23.008,9.254,25.424,11.361,25.424,15.887z M5.348,2.501
                            c-1.676,0-2.772,1.092-2.772,2.539c0,1.421,1.066,2.538,2.717,2.546h0.032c1.709,0,2.771-1.132,2.771-2.546
                            C8.054,3.593,7.019,2.501,5.343,2.501H5.348z M2.867,24.334h4.897V9.603H2.867V24.334z"/>
                    </svg>
                </span>
                <span class="text">{l s='Linkedin' mod='prestablog'}</span>
            </a>
        </li>
    </ul>
    <script src="{$prestablog_theme_dir}/js/rrssb.min.js"></script>
	{/if}
{/if}
<!-- /Module Presta Blog -->
