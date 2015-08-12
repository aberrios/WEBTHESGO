<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" colspan="2">{lang:mod_name} - {lang:details}</td>
 </tr>
 <tr>
  <td class="leftc">{lang:complete_result}</td>
  <td class="rightc"><a href="{url:cups_halloffame}">{lang:back}</a></td>
 </tr>
</table>
<br />
<table class="forum" cellpadding="0" cellspacing="{page:cellspacing}" style="width:{page:width}">
 <tr>
  <td class="headb" width="10">#</td>
  <td class="headb" width="18"></td>
  <td class="headb">{lang:name}</td>
 </tr>
{loop:result}
 <tr>
  <td class="rightc" width="10">{result:pos}</td>
  <td class="leftb" width="18">{result:img}</td>
  <td class="leftb">{result:link}</td>
 </tr>
{stop:result}
</table>
