{extends file="parent:frontend/checkout/shipping_payment.tpl"}

{block name="frontend_index_header_javascript_inline"}
    {$smarty.block.parent}
    ;var netiHidePaymentSelect = {if $netiHidePaymentSelect}true{else}false{/if};
{/block}
