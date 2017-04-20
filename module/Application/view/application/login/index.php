<?php

$form = $this->form;
/**
 *
 * Set form fields classes and placeholders here
 *
 */
$form->setAttributes(array(
    'class' => 'form'
));
$form->get('usernameOrEmail')->setAttributes(array(
    'class' => 'form-control input-lg', 
    'placeholder' => $this->translate('Username or Email')
));
$form->get('password')->setAttributes(array(
    'class' => 'form-control input-lg', 
    'placeholder' => $this->translate('Password')
));
$form->get('rememberme')->setAttributes(array(
    //'class' => 'form-control input-lg', 
));
$form->get('captcha')->setAttributes(array(
    'required' => 'true',
    'class' => 'form-control input-lg',
    'placeholder' => $this->translate('Verify you are human')
));
$form->get('submit')->setAttributes(array(
    'class' => 'btn btn btn-success btn-lg',
));
$form->prepare();
?>
<div class="jumbotron">
  <h2><?php echo $this->translate('Sign In')?></h2>
    <div class="container" id="wrap">
	  <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <?php echo $this->form()->openTag($form); ?>
            <div class="row">
              <div class="col-sm-12"> 
                <?php echo $this->messages ?>      
              </div>
            </div>
            <?php
              $element = $form->get('usernameOrEmail');
              echo $this->formElement($element);
              echo $this->formElementErrors($element);
            ?>
            <?php
              $element = $form->get('password');
              echo $this->formElement($element);
              echo $this->formElementErrors($element);
            ?>
            <div class="checkbox">
            <?php
              $element = $form->get('rememberme');
              echo $this->formLabel($element, $this->formCheckbox($element), 'append');
              echo $this->formElementErrors($element);
            ?>
            </div>
            <div class="row">
              <div class="col-xs-12 col-md-12">
                <?php 
                  $element = $form->get('captcha');
                  echo $this->formElement($element);
                  echo $this->formElementErrors($element);
                ?>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12"> 
                <?php echo $this->formRow($form->get('submit'))?>              
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12"> 
	            <a href="<?php echo $this->url('login', array('action' => 'signup'));?>">
	              <?php echo $this->translate('Sign up'); ?>
	            </a> |  
	            <a href="<?php echo $this->url('login', array('action' => 'reset-password'));?>">
	              <?php echo $this->translate('Forgot your password?'); ?>
	            </a>
              </div>
            </div>
            <?php 
              echo $this->formRow($form->get('csrf'));
              echo $this->form()->closeTag();
            ?>         
        </div>
      </div>            
    </div>
</div>