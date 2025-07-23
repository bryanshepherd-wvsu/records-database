<?php
class SoccerQueryManager {
    public static function get($level, $stat) {
        $config = self::getQueryConfig();
        return isset($config[$level][$stat]) ? $config[$level][$stat] : null;
    }

    private static function getQueryConfig() {
        return [
            'GAM' => [
                'points' => "SELECT * FROM `wsoc_boxscores` as b LEFT JOIN `wsoc_games` AS g ON b.GameID=g.GameID LEFT JOIN wsoc_athletes as a ON b.AthleteID=a.AthleteID WHERE `Stat`='%s' AND `Value` > 1 AND `Name`!='TEAM' ORDER BY `Value` DESC",
                'saves' => "SELECT * FROM `wsoc_boxscores` as b LEFT JOIN `wsoc_games` AS g ON b.GameID=g.GameID LEFT JOIN wsoc_athletes as a ON b.AthleteID=a.AthleteID WHERE `Stat`='%s' AND `Name`!='TEAM' ORDER BY `Value` DESC",
                'goals' => "SELECT * FROM `wsoc_boxscores` WHERE `Stat`='%s' ORDER BY `Value` DESC",
                'assists' => "SELECT * FROM `wsoc_boxscores` WHERE `Stat`='%s' ORDER BY `Value` DESC",
                'shots' => "SELECT * FROM `wsoc_boxscores` WHERE `Stat`='%s' ORDER BY `Value` DESC"
            ],
            'TMG' => [
                'points' => "SELECT * FROM `wsoc_boxscores` WHERE `Stat`='%s' ORDER BY `Value` DESC",
                'saves' => "SELECT * FROM `wsoc_boxscores` WHERE `Stat`='%s' ORDER BY `Value` DESC",
                'goals' => "SELECT * FROM `wsoc_boxscores` WHERE `Stat`='%s' ORDER BY `Value` DESC",
                'assists' => "SELECT * FROM `wsoc_boxscores` WHERE `Stat`='%s' ORDER BY `Value` DESC",
                'shots' => "SELECT * FROM `wsoc_boxscores` WHERE `Stat`='%s' ORDER BY `Value` DESC"
            ],
            'TMS' => [
                'points' => "SELECT Name,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,ROUND(AVG(Value),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` LEFT JOIN wsoc_athletes ON boxscores.AthleteID=athletes.AthleteID RIGHT JOIN wsoc_games ON boxscores.GameID=games.GameID WHERE `Name`='TEAM' AND `Stat`='%s' GROUP BY Name,Season ORDER BY PointsPerGame DESC) AS x WHERE 1",
                'saves' => "SELECT Name,SavesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,ROUND(AVG(Value),3) as SavesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` LEFT JOIN wsoc_athletes ON boxscores.AthleteID=athletes.AthleteID RIGHT JOIN wsoc_games ON boxscores.GameID=games.GameID WHERE `Name`='TEAM' AND `Stat`='%s' GROUP BY Name,Season ORDER BY SavesPerGame DESC) AS x WHERE 1",
                'saves_avg' => "SELECT Name,ShotsFaced,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END) as ShotsFaced,SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END) as saves, ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` LEFT JOIN wsoc_athletes ON boxscores.AthleteID=athletes.AthleteID RIGHT JOIN wsoc_games ON boxscores.GameID=games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY savesPerGame DESC) AS x WHERE 1"
            ],
            'SEA' => [
                'goals_pergame' => "SELECT Name,GamesPlayed,GoalsScored,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as GamesPlayed,SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END) as GoalsScored, ROUND(SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` LEFT JOIN wsoc_athletes ON boxscores.AthleteID=athletes.AthleteID RIGHT JOIN wsoc_games ON boxscores.GameID=games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season HAVING GamesPlayed > 4 ORDER BY GoalsPerGame DESC",
                'assists_pergame' => "SELECT Name,GamesPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as GamesPlayed,SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)as Assists, ROUND(SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` LEFT JOIN wsoc_athletes ON boxscores.AthleteID=athletes.AthleteID RIGHT JOIN wsoc_games ON boxscores.GameID=games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season HAVING GamesPlayed > 4 ORDER BY AssistsPerGame DESC)",
                'saves_pergame' => "SELECT Name,GamesPlayed,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as GamesPlayed,SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as saves, ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` LEFT JOIN wsoc_athletes ON boxscores.AthleteID=athletes.AthleteID RIGHT JOIN wsoc_games ON boxscores.GameID=games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season HAVING GamesPlayed > 4 ORDER BY savesPerGame DESC)",
                // Add remaining SEA stats...
            ],
            'CAR' => [
                'goals_pergame' => "SELECT Name,GamesPlayed,GoalsScored,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as GamesPlayed,SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)as GoalsScored, ROUND(SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` LEFT JOIN wsoc_athletes ON boxscores.AthleteID=athletes.AthleteID RIGHT JOIN wsoc_games ON boxscores.GameID=games.GameID WHERE `Name`!='TEAM' GROUP BY Name HAVING GamesPlayed > 4 ORDER BY GoalsPerGame DESC)",
                'assists_pergame' => "SELECT Name,GamesPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as GamesPlayed,SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)as Assists, ROUND(SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` LEFT JOIN wsoc_athletes ON boxscores.AthleteID=athletes.AthleteID RIGHT JOIN wsoc_games ON boxscores.GameID=games.GameID WHERE `Name`!='TEAM' GROUP BY Name HAVING GamesPlayed > 4 ORDER BY AssistsPerGame DESC)",
                // Add remaining CAR stats...
            ],
            'ATH' => [
                'active' => "SELECT * FROM wsoc_athletes WHERE `Status`='A' ORDER BY Name",
                'retired' => "SELECT * FROM wsoc_athletes WHERE `Status`='R' ORDER BY Name"
            ]
        ];
    }

    public static function getAllLevels() {
        return [
            'GAM' => 'Individual Game Highs',
            'SEA' => 'Individual Season Highs',
            'CAR' => 'Career Highs',
            'TMG' => 'Team Game Highs',
            'TMS' => 'Team Season Highs',
            'ATH' => 'Active Athletes',
            'ATR' => 'Retired Athletes'
        ];
    }

    public static function getAllStats($level) {
        $baseStats = [
            'GAM' => ['points', 'saves', 'goals', 'assists', 'shots'],
            'TMG' => ['points', 'saves', 'goals', 'assists', 'shots'],
            'TMS' => ['points', 'saves', 'saves_avg', 'goals_against_avg'],
            'SEA' => [
                'goals', 'goals_pergame', 'assists', 'assists_pergame',
                'points', 'points_pergame', 'shots', 'shots_pergame',
                'sog', 'sog_pergame', 'pengoals', 'penshots',
                'type_gw', 'saves', 'saves_pergame', 'saves_avg',
                'goals_against_avg', 'win', 'loss', 'tie', 'played',
                'starts', 'minutes', 'goalie_played', 'goalie_starts',
                'goalie_minutes'
            ],
            'CAR' => [
                'goals', 'goals_pergame', 'assists', 'assists_pergame',
                'points', 'points_pergame', 'shots', 'shots_pergame',
                'sog', 'sog_pergame', 'saves', 'saves_pergame',
                'saves_avg', 'goals_against_avg', 'played', 'starts',
                'minutes', 'goalie_played', 'goalie_starts',
                'goalie_minutes'
            ]
        ];

        return isset($baseStats[$level]) ? $baseStats[$level] : [];
    }
}