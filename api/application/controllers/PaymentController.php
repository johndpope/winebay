<?php
class PaymentController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function makepaymentAction() {
        //TODO Conectar API YesPay - Efetuar Cobrança
    }

    public function undopaymentAction() {
        //TODO Conectar API YesPay - Iniciar Processo de Estorno
    }
}
