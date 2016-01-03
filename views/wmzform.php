<?
    $this->data['title']='Пополнение счёта через WebMoney';
$this->data['content']='
<h2>'.icon('64x64/credit_cart.png').'Пополнение счёта через WebMoney</h2>
<h3>Сейчас курсы:</h3>
'.br(1).'1$ = '.config('KURS').lang('off_money_donate').'<br>'.
lang('off_money_donate').' = '.config('DR')/config('KURS').' рубля<br>
'.config('DU').' грн  = '.config('KURS').' '.lang('off_money_donate');

    $this->table->add_row(form_open('shop/wmzpay').b('WMZ').form_input('pay','1').'$',form_submit('mysubmit', 'Пополнить').'</form>');
    $this->table->add_row(form_open('shop/wmupay').b('WMU').form_input('pay',$this->config->item('DU')).'Гривен',form_submit('mysubmit', 'Пополнить').'</form>');
    $this->table->add_row(form_open('shop/wmrpay').b('WMR').form_input('pay',$this->config->item('DR')).'рублей',form_submit('mysubmit', 'Пополнить').'</form>');
    $this->data['content'].=$this->table->generate();
    ?>