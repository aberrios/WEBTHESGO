<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb">{lang:mod_name} - {lang:halloffame}</td>
 </tr>
 <tr>
  <td class="leftb">{icon:contents} {lang:total}: {count:all}</td>
 </tr>
 <tr>
  <td class="leftb">
    <form method="post" id="gamechoice" action="{url:cups_halloffame}">
    {lang:game}:
    <select name="games_id">
      <option value="0">----</option>{loop:games}
      <option value="{games:games_id}"{games:selection}>{games:games_name}</option>{stop:games}
    </select>
    <input type="submit" name="submit" value="{lang:show}" /></form>
  </td>
 </tr>
</table>
<br />

<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
{if:hascups}
 <tr>
  <td class="headb">{lang:game}</td>
  <td class="headb">{lang:name}</td>
  <td class="headb">{lang:cup_start}</td>
  <td class="headb">{lang:cup_system}</td>
  <td class="headb">{lang:result}</td>
 </tr>{loop:cups}
 <tr>
  <td class="leftc" valign="top">{cups:game_icon}</td>
  <td class="leftc" valign="top"><a href="{url:cups_view:id={cups:cups_id}}">{cups:cups_name}</a></td>
  <td class="leftc" valign="top">{cups:cups_start}</td>
  <td class="leftc" valign="top">{cups:cups_system}</td>
  <td class="leftc" valign="top">{unless:open}
		<table>
			<tr>
				<td align="left">{image:gold}</td>
				<td align="left">{cups:winner}</td>
			</tr>
			<tr>
				<td align="left">{image:silver}</td>
				<td align="left">{cups:second}</td>
			</tr>
			{loop:third}
			<tr>
				<td align="left">{image:bronze}</td>
				<td align="left">{third:name}</td>
			</tr>
			{stop:third}
			<tr>
				<td colspan="2"><br /><a href="{url:cups_result:id={cups:cups_id}}">{lang:complete_result}</a></td>
			</tr>
		</table>
	{stop:open}
	{if:open}-{stop:open}
  </td>
 </tr>{stop:cups}
{stop:hascups}
{unless:hascups}
 <tr>
  <td class="headb">{lang:no_data}</td>
 </tr>
{stop:hascups}
</table>
