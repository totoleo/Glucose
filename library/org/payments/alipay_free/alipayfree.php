<?php

/**
 *    支付宝免签支付方式 
 *
 *    @author    Garbin
 *    @usage    none
 */
class AlipayfreePayment {

    function get_code($order,$payment) {

        ?>
		<body onLoad="javascript:document.E_FORM.submit()">
            <form name="E_FORM" action="https://shenghuo.alipay.com/send/payment/fill.htm" method="post">  
                <input type="hidden" name="optEmail" value="<?php echo $payment['alipayfree_account']; ?>">  
                <input type="hidden" name="payAmount" value="<?php echo $order['order_amount'];?>">  
                <input type="hidden" name="title" value="wsy">  
                <input type="hidden" name="memo" value="<?php echo $this->_get_memo($order);?>">
                <input type="hidden" name="isSend" value="true"/>
                <input type="hidden" name="smsNo" value="">  
            </form>  
        </body>
        <?php
        exit;
        
    }
    
    
    function _get_memo($order){
        $text = "Order_id:".$order['order_id'].";Amount:".$order['order_amount'];
        return $text;
    }
 
}
?>