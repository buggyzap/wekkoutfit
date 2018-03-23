{if isset($weekoutfit_status) && $weekoutfit_status == 1}
<div id="weekoutfit_block_home" class="block">
  <div class="week_content">
    <div class="week_viewport">
      {foreach $outfits as $outfit}
      <div class="week_item">
        <div class="row text-center">
          <h2 class="weekoutfit_name">{$outfit->weekoutfit_name}</h2>
          <p class="weekoutfit_desc">{$outfit->weekoutfit_desc}</p>
        </div>
        <div class="col-md-8 week_image">
          {foreach $outfit->points as $punto}
          <div class="point hidden-xs" style="left:{$punto->xPercent}%;top:{$punto->yPercent}%;">
            <div class="point_content">
              <div class="col-xs-4"><img src="{$punto->product_image}" style="width:100%;" /></div>
              <div class="col-xs-8">
                <h3 class="product-name">{$punto->product_name}</h3>
                <span class="price">{convertPrice price=$punto->product_price}</span>
                <a href="{$punto->product_link}">{l s='VAI AL PRODOTTO' mod='weekoutfit'}</a>
              </div>
            </div>
          </div>
          {/foreach}
          <img src="img/{$outfit->image}" style="width:100%;" />
         </div>
        <div class="col-md-4 week_products">
           <div class="row">
             <div class="week_list">
               {foreach $outfit->points as $punto}
               <div class="col-xs-6 col-sm-3 col-lg-6 col-md-6">
                 <div class="week_product_item">
                   <a href="#">
                     <img src="{$punto->product_image}" style="width:100%;" />
                     <div class="week_product_item_info">
                       <h3 class="product-name">{$punto->product_name}</h3>
                       <span class="price">{convertPrice price=$punto->product_price}</span>
                     </div>
                    </a>
                  </div>
               </div>
               {/foreach}
             </div>
           </div>
         </div>
      </div>
      {/foreach}
      {if count($outfits) > 1}
      <div class="controls">
        <div class="prev"><span class="lnr lnr-chevron-left"></span></div>
        <div class="next"><span class="lnr lnr-chevron-right"></span></div>
      </div>
      {/if}
    </div>
  </div>
</div>
{/if}
