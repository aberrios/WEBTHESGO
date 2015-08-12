/* Cups style settings */

table.cup-grid
{
	background-color: #{grid:color_bg};
	{grid:width}
	{grid:height}
	border: 0px;
	border-spacing: 0px 0px; /* cellspacing=0 */
}

/* winner of the winner bracket or cup (if no double elimination) */
.cup-grid-winner
{
	white-space: nowrap;
	overflow: hidden;
	background-color: #{grid:color_team_bg};
	color: #{grid:color_team_font};
}

/* winner of the loser bracket */
.cup-grid-winner-lb
{
	white-space: nowrap;
	overflow: hidden;
	background-color: #{grid:color_team_bg};
	color: #{grid:color_team_font};
}

/* general team in the WB or LB */
.cup-grid-team
{
	white-space: nowrap;
	overflow: hidden;
	background-color: #{grid:color_team_bg};
	color: #{grid:color_team_font};
}

/* general team win in the WB or LB */
.cup-grid-team-win
{
	white-space: nowrap;
	overflow: hidden;
	background-color: #{grid:color_team_bg};
	color: #{grid:color_team_font};
}

/* general team loss in the WB or LB */
.cup-grid-team-loss
{
	white-space: nowrap;
	overflow: hidden;
	background-color: #{grid:color_team_bg};
	color: #{grid:color_team_font};
}

/* general score in the WB or LB */
.cup-grid-score
{
	white-space: nowrap;
	overflow: hidden;
	width: {grid:score_width}px;
	background-color: #{grid:color_team_bg};
	color: #{grid:color_team_font};
}

/* general score win in the WB or LB */
.cup-grid-score-win
{
	white-space: nowrap;
	overflow: hidden;
	width: {grid:score_width}px;
	background-color: #{grid:color_team_bg};
	color: #{grid:color_team_font};
}

/* general score loss in the WB or LB */
.cup-grid-score-loss
{
	white-space: nowrap;
	overflow: hidden;
	width: {grid:score_width}px;
	background-color: #{grid:color_team_bg};
	color: #{grid:color_team_font};
}

/* team coming from WB and joining LB */
.cup-grid-team-lb
{
	white-space: nowrap;
	overflow: hidden;
	background-color: #{grid:color_team_bg_lb};
	color: #{grid:color_team_font};
}

/* win team coming from WB and joining LB */
.cup-grid-team-lb-win
{
	white-space: nowrap;
	overflow: hidden;
	background-color: #{grid:color_team_bg_lb};
	color: #{grid:color_team_font};
}

/* loss team coming from WB and joining LB */
.cup-grid-team-lb-loss
{
	white-space: nowrap;
	overflow: hidden;
	background-color: #{grid:color_team_bg_lb};
	color: #{grid:color_team_font};
}

/* score from team coming from WB and joining LB */
.cup-grid-score-lb
{
	white-space: nowrap;
	overflow: hidden;
	width: {grid:score_width}px;
	background-color: #{grid:color_team_bg_lb};
	color: #{grid:color_team_font};
}

/* win score from team coming from WB and joining LB */
.cup-grid-score-lb-win
{
	white-space: nowrap;
	overflow: hidden;
	width: {grid:score_width}px;
	background-color: #{grid:color_team_bg_lb};
	color: #{grid:color_team_font};
}

/* loss score from team coming from WB and joining LB */
.cup-grid-score-lb-loss
{
	white-space: nowrap;
	overflow: hidden;
	width: {grid:score_width}px;
	background-color: #{grid:color_team_bg_lb};
	color: #{grid:color_team_font};
}

/*
 * grid images 
 */

/* general images */
.cup-grid-angle-down
{
	background: url({page:path}../../uploads/cups/cup-grid-angle-down.png);
	background-repeat: no-repeat;
	width: {grid:image_width}px;
	height: {grid:image_height}px;
}

.cup-grid-angle-up
{
	background: url({page:path}../../uploads/cups/cup-grid-angle-up.png);
	background-repeat: no-repeat;
	width: {grid:image_width}px;
	height: {grid:image_height}px;
}

.cup-grid-vertical-split-right
{
	background: url({page:path}../../uploads/cups/cup-grid-vertical-split-right.png);
	background-repeat: no-repeat;
	width: {grid:image_width}px;
	height: {grid:image_height}px;
}

.cup-grid-vertical
{
	background: url({page:path}../../uploads/cups/cup-grid-vertical.png);
	background-repeat: no-repeat;
	width: {grid:image_width}px;
	height: {grid:image_height}px;
}

/* LB images */
.cup-grid-angle-down-right
{
	background: url({page:path}../../uploads/cups/cup-grid-angle-down-right.png);
	background-repeat: no-repeat;
	width: {grid:image_width}px;
	height: {grid:image_height}px;
}

.cup-grid-angle-up-right
{
	background: url({page:path}../../uploads/cups/cup-grid-angle-up-right.png);
	background-repeat: no-repeat;
	width: {grid:image_width}px;
	height: {grid:image_height}px;
}

.cup-grid-horizontal
{
	background: url({page:path}../../uploads/cups/cup-grid-horizontal.png);
	background-repeat: no-repeat;
	width: {grid:image_width}px;
	height: {grid:image_height}px;
}
