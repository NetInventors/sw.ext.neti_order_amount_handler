{extends file='parent:frontend/checkout/confirm.tpl'}

{block name='frontend_checkout_confirm_error_messages'}
    {$smarty.block.parent}
    {if '1' === $smarty.get.zero_payment_error}
        {include file="frontend/_includes/messages.tpl" type="error" content="{s namespace='plugins/neti_order_amount_handler/frontend/checkout' name='zero_payment_error'}{/s}"}
    {/if}
{/block}
