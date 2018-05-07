{extends file="parent:frontend/checkout/change_payment.tpl"}
{namespace name="plugins/neti_order_amount_handler/frontend/checkout"}

{block name="frontend_checkout_payment_content"}
    {include file="frontend/_includes/messages.tpl" type='info' visible=false content="{s name="change_payment_info"}{/s}"}
    {$smarty.block.parent}
{/block}