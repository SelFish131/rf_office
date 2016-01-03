<h2>Смена пола</h2>
[STEP1]
	<p>Выберите персонажа.</p>
	<br>
	<p>
		<form action="{step2url}" method="post">
		{charlist}
		<input type="submit" name="submit" value="{lang_service_next}"  /></form>
	</p>
[/STEP1]
[STEP2]
	<p>Вы уверены что хотите сменить пол персонажа <b>{char}</b> на {sex}? <br>
		Это будет стоить {changegp} GP</p>
	{step3}
[/STEP2]
[ERROR]
К сожаленю ваша раса не имеет возможности сменить пол.
[/ERROR]
[OK]
Пол успешно изменён.
[/OK]
<?=go_back('shop',true)?>