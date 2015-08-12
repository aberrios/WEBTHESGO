<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="headb">{lang:mod_name} - {lang:options}</td>
	</tr>
	<tr>
		<td class="leftb"></td>
	</tr>
</table>
<br />

<form method="post" id="cups_options" action="{url:cups_options}">
<table class="forum" style="width:{page:width}" cellpadding="0" cellspacing="{page:cellspacing}">
	<tr>
		<td class="leftc">{icon:cal} {lang:html_or_image}</td>
		<td class="leftb"> {lang:html}<input type="radio" name="html" value="1" {com:html_yes}/> /
							<input type="radio" name="html" value="0" {com:html_no}/> {lang:image} </td>
	</tr>
	<tr>
		<td class="leftc" colspan="2">{lang:options}: {lang:html}</td>
	</tr>
	<tr>
		<td class="leftc">{icon:cal} {lang:show_scores}</td>
		<td class="leftb">{lang:yes} <input type="radio" name="scores" value="1" {com:scores_yes}/> /
							<input type="radio" name="scores" value="0" {com:scores_no}/> {lang:no} </td>
	</tr>
	<tr>
		<td class="leftc" colspan="2">{lang:options}: {lang:image}</td>
	</tr>
	<tr>
		<td class="leftc">{icon:thumbnail} {lang:lightbox}</td>
		<td class="leftb">
			<select name="lightbox">
				{lightbox:options}
			</select>
		</td>
	</tr>
	<tr>
		<td class="leftc">{icon:resizecol} {lang:brackets_width_lightbox}</td>
		<td class="leftb"><input type="text" name="width_lightbox" value="{com:width_lightbox}" maxlength="4" size="4" /></td>
	</tr>
	<tr>
		<td class="leftc" colspan="2">{lang:options}: {lang:html} &amp; {lang:image}</td>
	</tr>
	<tr>
		<td class="leftc">{icon:resizecol} {lang:brackets_width}</td>
		<td class="leftb"><input type="text" name="width" value="{com:width}" maxlength="4" size="4" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:resizerow} {lang:brackets_height}</td>
		<td class="leftb"><input type="text" name="height" value="{com:height}" maxlength="4" size="4" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:xpaint} {lang:color_bg}</td>
		<td class="leftb"><input type="text" name="color_bg" value="{com:color_bg}" maxlength="11" size="12" /> [{com:sample_bg}]</td>
	</tr>
	<tr>
		<td class="leftc">{icon:xpaint} {lang:color_line}</td>
		<td class="leftb"><input type="text" name="color_line" value="{com:color_line}" maxlength="11" size="12" /> [{com:sample_line}]</td>
	</tr>
	<tr>
		<td class="leftc">{icon:xpaint} {lang:color_team_bg}</td>
		<td class="leftb"><input type="text" name="color_team_bg" value="{com:color_team_bg}" maxlength="11" size="12" /> [{com:sample_team_bg}]</td>
	</tr>
	<tr>
		<td class="leftc">{icon:xpaint} {lang:color_team_bg_lb}</td>
		<td class="leftb"><input type="text" name="color_team_bg_lb" value="{com:color_team_bg_lb}" maxlength="11" size="12" /> [{com:sample_team_bg_lb}]</td>
	</tr>
	<tr>
		<td class="leftc">{icon:xpaint} {lang:color_team_fg}</td>
		<td class="leftb"><input type="text" name="color_team_fg" value="{com:color_team_fg}" maxlength="11" size="12" /> [{com:sample_team_fg}]</td>
	</tr>
	<tr>
		<td class="leftc">{icon:xpaint} {lang:color_headline}</td>
		<td class="leftb"><input type="text" name="color_headline" value="{com:color_headline}" maxlength="11" size="12" /> [{com:sample_headline}]</td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:title1}</td>
		<td class="leftb"><input type="text" name="title1" value="{com:title1}" maxlength="80" size="20" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:xpaint} {lang:color_title1}</td>
		<td class="leftb"><input type="text" name="color_title1" value="{com:color_title1}" maxlength="11" size="12" /> [{com:sample_title1}]</td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:title2}</td>
		<td class="leftb"><input type="text" name="title2" value="{com:title2}" maxlength="80" size="20" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:xpaint} {lang:color_title2}</td>
		<td class="leftb"><input type="text" name="color_title2" value="{com:color_title2}" maxlength="11" size="12" /> [{com:sample_title2}]</td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:max_gridname}</td>
		<td class="leftb"><input type="text" name="max_gridname" value="{com:max_gridname}" maxlength="2" size="2" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:max_navlist}</td>
		<td class="leftb"><input type="text" name="max_navlist" value="{com:max_navlist}" maxlength="2" size="2" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:max_headline}</td>
		<td class="leftb"><input type="text" name="max_headline" value="{com:max_headline}" maxlength="2" size="2" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:notify_hours}</td>
		<td class="leftb"><input type="text" name="notify_hours" value="{com:notify_hours}" maxlength="2" size="2" /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:notify_via_pm}</td>
		<td class="leftb"><input type="checkbox" name="notify_pm" value="1" {com:notify_pm} /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:playlist} {lang:notify_via_email}</td>
		<td class="leftb"><input type="checkbox" name="notify_email" value="1" {com:notify_email} /></td>
	</tr>
	<tr>
		<td class="leftc">{icon:ksysguard} {lang:options}</td>
		<td class="leftb">
			<input type="submit" name="submit" value="{lang:edit}" />
			<input type="reset" name="reset" value="{lang:reset}" />
		</td>
	</tr>
</table>
</form>
