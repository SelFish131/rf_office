<?
    $this->data['title']='���������� ����� ����� WebMoney';
$this->data['content']='
<h2>'.icon('64x64/credit_cart.png').'���������� ����� ����� WebMoney</h2>
<h3>������ �����:</h3>
'.br(1).'1$ = '.config('KURS').lang('off_money_donate').'<br>'.
lang('off_money_donate').' = '.config('DR')/config('KURS').' �����<br>
'.config('DU').' ���  = '.config('KURS').' '.lang('off_money_donate');

    $this->table->add_row(form_open('shop/wmzpay').b('WMZ').form_input('pay','1').'$',form_submit('mysubmit', '���������').'</form>');
    $this->table->add_row(form_open('shop/wmupay').b('WMU').form_input('pay',$this->config->item('DU')).'������',form_submit('mysubmit', '���������').'</form>');
    $this->table->add_row(form_open('shop/wmrpay').b('WMR').form_input('pay',$this->config->item('DR')).'������',form_submit('mysubmit', '���������').'</form>');
    $this->data['content'].=$this->table->generate();
    ?>