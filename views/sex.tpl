<h2>����� ����</h2>
[STEP1]
	<p>�������� ���������.</p>
	<br>
	<p>
		<form action="{step2url}" method="post">
		{charlist}
		<input type="submit" name="submit" value="{lang_service_next}"  /></form>
	</p>
[/STEP1]
[STEP2]
	<p>�� ������� ��� ������ ������� ��� ��������� <b>{char}</b> �� {sex}? <br>
		��� ����� ������ {changegp} GP</p>
	{step3}
[/STEP2]
[ERROR]
� �������� ���� ���� �� ����� ����������� ������� ���.
[/ERROR]
[OK]
��� ������� ������.
[/OK]
<?=go_back('shop',true)?>