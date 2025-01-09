<?php

namespace App\Consts;

// 定数
class MpConsts
{
//  public const MP_CSV_PATH_BASE = '/usr/local/moss_point/mp/input/';
  public const MP_CSV_PATH_BASE = '/usr/local/moss_point/mp/laravel/storage/app/csv/';
  public const TRACK_TYPE_NONE = 0; // 未設定
  public const TRACK_TYPE_TERF = 1; // 芝
  public const TRACK_TYPE_DART = 2; // ダート
  public const TRACK_TYPE_SHOGAI = 3; // 障害芝
  public const TRACK_TYPE_SHOGAI_DART = 4; // 障害ダート
  public const TRACK_TYPE_TERF_STRAIGHT = 5; // 芝直線

  public const TRACK_BIAS_NONE = 0; // 未設定
  public const TRACK_BIAS_GOOD = 1; // 良馬場
  public const TRACK_BIAS_NORMAL = 2; // 稍重
  public const TRACK_BIAS_BAD = 3; // 重
  public const TRACK_BIAS_VERY_BAD = 4; // 不良

  public const CORNER_DIRECTION_NONE = 0; // コーナー向き未設定
  public const CORNER_DIRECTION_RIGHT = 1; // コーナー向き右
  public const CORNER_DIRECTION_LEFT = 2; // コーナー向き左
  public const CORNER_DIRECTION_STRAIGHT = 3; // 直線

  public const CORNER_SIZE_NORMAL = 1; // 設定なし
  public const CORNER_SIZE_BIG = 2; // 外回り
  public const CORNER_SIZE_SMALL = 3; // 内回り

  public const PLACE_ID_LIST = [
    1 => "sapporo",
    2 => "hakodate",
    3 => "fukushima",
    4 => "niigata",
    5 => "tokyo",
    6 => "nakayama",
    7 => "chukyo",
    8 => "kyoto",
    9 => "hanshin",
    10 => "kokura",
  ];

  public const PLACE_ID_JP_LIST = [
    1 => "札幌",
    2 => "函館",
    3 => "福島",
    4 => "新潟",
    5 => "東京",
    6 => "中山",
    7 => "中京",
    8 => "京都",
    9 => "阪神",
    10 => "小倉",
  ];

	public const CLASS_CODE_NAME_LIST = [
		7 => "未勝利",
		11 => "未出走",
		15 => "新馬",
		19 => "400万下",
		23 => "500万下",
		39 => "900万下",
		43 => "1000万下",
		63 => "1500万下",
		67 => "1600万下",
		115 => "準OP",
		131 => "重賞外OP",
		147 => "G無し重賞",
		163 => "G3",
		179 => "G2",
		195 => "G1",
	];

  public const CORNER_DIRECTION_LIST = [
    self::CORNER_DIRECTION_NONE => "未設定",
    self::CORNER_DIRECTION_RIGHT => "右",
    self::CORNER_DIRECTION_LEFT => "左",
    self::CORNER_DIRECTION_STRAIGHT => "直線",
  ];

  public const TRACK_TYPE_LIST = [
    self::TRACK_TYPE_TERF => '芝',
    self::TRACK_TYPE_DART => 'ダート',
    self::TRACK_TYPE_SHOGAI => '障害・直線・芝',
    self::TRACK_TYPE_SHOGAI_DART => '障害・直線・ダート',
  ];

  public const TRACK_CODE_LIST = [
    0 => '芝',
    1 => 'ダート',
    2 => '障害・直線・芝',
    3 => '障害・直線・ダート',
  ];
  
  public const YOUBI_LIST = [
    1 => '日',
    2 => '月',
    3 => '火',
    4 => '水',
    5 => '木',
    6 => '金',
    7 => '土'
  ];

  public const TRACK_CODE_TO_TRACK_TYPE = [
    0 => ['track_type' => self::TRACK_TYPE_TERF, 'corner_size' => self::CORNER_SIZE_NORMAL],
    1 => ['track_type' => self::TRACK_TYPE_DART, 'corner_size' => self::CORNER_SIZE_NORMAL],
    2 => ['track_type' => self::TRACK_TYPE_SHOGAI, 'corner_size' => self::CORNER_SIZE_NORMAL],
    3 => ['track_type' => self::TRACK_TYPE_SHOGAI_DART, 'corner_size' => self::CORNER_SIZE_NORMAL],
    8 => ['track_type' => self::TRACK_TYPE_TERF, 'corner_size' => self::CORNER_SIZE_BIG],
  ];

  // 1:晴,2:曇,3:小雨,4:雨,5:小雪,6雪
  public const TENKI_CODE = [
    0 => 'None',
    1 => '晴',
    2 => '曇',
    3 => '小雨',
    4 => '雨',
    5 => '小雪',
    6 => '雪',
  ];

  // 1:良,2:稍重,3:重,4:不良
  public const TRACK_BIAS_CODE = [
    self::TRACK_BIAS_GOOD => '良',
    self::TRACK_BIAS_NORMAL => '稍',
    self::TRACK_BIAS_BAD => '重',
    self::TRACK_BIAS_VERY_BAD => '不',
  ];

  public const TRACK_CODE_BIAS_TYPE = [
    11 => ['type_name' => '芝良', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => self::TRACK_BIAS_GOOD], // 芝
    12 => ['type_name' => '芝稍重', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => self::TRACK_BIAS_NORMAL], // 芝
    13 => ['type_name' => '芝重', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => self::TRACK_BIAS_BAD], // 芝
    14 => ['type_name' => '芝不良', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => self::TRACK_BIAS_VERY_BAD], // 芝
    21 => ['type_name' => 'ダート良', 'track_type' => self::TRACK_TYPE_DART, 'track_bias' => self::TRACK_BIAS_GOOD], // ダート
    22 => ['type_name' => 'ダート稍重', 'track_type' => self::TRACK_TYPE_DART, 'track_bias' => self::TRACK_BIAS_NORMAL], // ダート
    23 => ['type_name' => 'ダート重', 'track_type' => self::TRACK_TYPE_DART, 'track_bias' => self::TRACK_BIAS_BAD], // ダート
    24 => ['type_name' => 'ダート不良', 'track_type' => self::TRACK_TYPE_DART, 'track_bias' => self::TRACK_BIAS_VERY_BAD], // ダート
    70 => ['type_name' => 'クッション値7以下', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 70], // 芝クッション値7以下
    75 => ['type_name' => 'クッション値7.5', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 75], // 芝クッション値7.5
    80 => ['type_name' => 'クッション値8', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 80], // 芝クッション値8
    85 => ['type_name' => 'クッション値8.5', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 85], // 芝クッション値8.5
    90 => ['type_name' => 'クッション値9', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 90], // 芝クッション値9
    95 => ['type_name' => 'クッション値9.5', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 95], // 芝クッション値9.5
    100 => ['type_name' => 'クッション値10', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 100], // 芝クッション値10
    105 => ['type_name' => 'クッション値10.5', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 105], // 芝クッション値10.5
    110 => ['type_name' => 'クッション値11以上', 'track_type' => self::TRACK_TYPE_TERF, 'track_bias' => 110], // 芝クッション値11
  ];
  //67:馬印1 1:◎,2:◯,3:▲,4:△,0:無印
  public const HORSE_MARK_1_LIST = [
    1 => '◎',
    2 => '◯',
    3 => '▲',
    4 => '△',
    5 => '☓',
    0 => '',
  ];

  //68:馬印2 1:逃,2:先,3:マ,4:差,5:追,6:中,7:後,8:遅,0:初
  public const HORSE_MARK_2_LIST = [
    1 => '逃',
    2 => '先',
    3 => 'マ',
    4 => '差',
    5 => '追',
    6 => '中',
    7 => '後',
    8 => '遅',
    0 => '初',
  ];
  //69:馬印3 1:SS,2:S,3:A+,4:A,5:B+,6:B,7:C,8:D,9:不,0:初
  public const HORSE_MARK_3_LIST = [
    1 => 'SS',
    2 => 'S',
    3 => 'A+',
    4 => 'A',
    5 => 'B+',
    6 => 'B',
    7 => 'C',
    8 => 'D',
    9 => '不',
    0 => '',
  ];
  //70:馬印4 1:◎,2:◯,3:▲,4:▲,0:無印
  public const HORSE_MARK_4_LIST = [
    1 => '◎',
    2 => '◯',
    3 => '▲',
    4 => '△',
    5 => '☓',
    0 => '',
  ];
  //71:馬印5 1:↑↑,2:↑,3:-,4:↓
  public const HORSE_MARK_5_LIST = [
    1 => '↑↑',
    2 => '↑',
    3 => '-',
    4 => '↓',
    0 => '',
  ];
  //72:馬印6 1:初,2:B,3:外,4:‐
  public const HORSE_MARK_6_LIST = [
    1 => '初',
    2 => 'B',
    3 => '外',
    4 => '‐',
    0 => '',
  ];

  //37:性別 1:牡,2:牝,3:せん
  public const HORSE_SEX_LIST = [
    1 => '牡',
    2 => '牝',
    3 => 'セ',
  ];
  public const BLINKER_LIST = [
    1 => 'B',
    0 => '',
  ];

  public const POINT_ID_LIST = [
    1 => 'MOP',
    2 => 'MTC',
    3 => 'ho9',
    4 => '距ho9',
    5 => 'P',
    6 => '前P',
    7 => '場P',
    8 => '距P',
    9 => 'ペP',
    10 => 'Cu_P',
    11 => 'TBP',
    12 => 'CTP',
    13 => 'MCP',
  ];

  public const SHOGAI_CORSE_LEVEL = [
    211 => ['corse_id' => 211, 'place_id' => 8, 'distance' => 3170, 'obstacle_width' => 226, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 44],
    214 => ['corse_id' => 214, 'place_id' => 8, 'distance' => 3170, 'obstacle_width' => 226, 'ss_count' => 1, 'tasuki' => 0, 's_level' => 43],
    216 => ['corse_id' => 216, 'place_id' => 8, 'distance' => 3930, 'obstacle_width' => 231, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 53],
    127 => ['corse_id' => 127, 'place_id' => 5, 'distance' => 3110, 'obstacle_width' => 239, 'ss_count' => 1, 'tasuki' => 0, 's_level' => 29],
    146 => ['corse_id' => 146, 'place_id' => 8, 'distance' => 2910, 'obstacle_width' => 243, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 36],
    83  => ['corse_id' => 87, 'place_id' => 7, 'distance' => 3000, 'obstacle_width' => 250, 'ss_count' => 2, 'tasuki' => 0, 's_level' => 23],
    56  => ['corse_id' => 56, 'place_id' => 5, 'distance' => 3000, 'obstacle_width' => 250, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 28],
    168 => ['corse_id' => 168, 'place_id' => 7, 'distance' => 3300, 'obstacle_width' => 254, 'ss_count' => 2, 'tasuki' => 0, 's_level' => 25],
    217 => ['corse_id' => 217, 'place_id' => 5, 'distance' => 3100, 'obstacle_width' => 258, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 28],
    164 => ['corse_id' => 164, 'place_id' => 9, 'distance' => 3110, 'obstacle_width' => 259, 'ss_count' => 0, 'tasuki' => 1, 's_level' => 34],
    167 => ['corse_id' => 167, 'place_id' => 9, 'distance' => 3140, 'obstacle_width' => 262, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 34],
    68  => ['corse_id' => 68, 'place_id' => 9, 'distance' => 2970, 'obstacle_width' => 270, 'ss_count' => 0, 'tasuki' => 1, 's_level' => 33],
    208 => ['corse_id' => 208, 'place_id' => 6, 'distance' => 3570, 'obstacle_width' => 274, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 34],
    165 => ['corse_id' => 165, 'place_id' => 9, 'distance' => 3900, 'obstacle_width' => 279, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 40],
    125 => ['corse_id' => 125, 'place_id' => 4, 'distance' => 3250, 'obstacle_width' => 295, 'ss_count' => 1, 'tasuki' => 0, 's_level' => 22],
    126 => ['corse_id' => 126, 'place_id' => 4, 'distance' => 3290, 'obstacle_width' => 299, 'ss_count' => 1, 'tasuki' => 0, 's_level' => 22],
    210 => ['corse_id' => 210, 'place_id' => 6, 'distance' => 3350, 'obstacle_width' => 305, 'ss_count' => 1, 'tasuki' => 0, 's_level' => 33],
    52  => ['corse_id' => 52, 'place_id' => 4, 'distance' => 2850, 'obstacle_width' => 317, 'ss_count' => 1, 'tasuki' => 0, 's_level' => 18],
    169 => ['corse_id' => 169, 'place_id' => 6, 'distance' => 3200, 'obstacle_width' => 320, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 31],
    162 => ['corse_id' => 162, 'place_id' => 6, 'distance' => 3210, 'obstacle_width' => 321, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 31],
    77  => ['corse_id' => 77, 'place_id' => 4, 'distance' => 2890, 'obstacle_width' => 321, 'ss_count' => 1, 'tasuki' => 0, 's_level' => 18],
    49  => ['corse_id' => 49, 'place_id' => 10, 'distance' => 2860, 'obstacle_width' => 322, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 24],
    161 => ['corse_id' => 161, 'place_id' => 10, 'distance' => 3390, 'obstacle_width' => 339, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 27],
    209 => ['corse_id' => 209, 'place_id' => 6, 'distance' => 4250, 'obstacle_width' => 354, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 37],
    50  => ['corse_id' => 50, 'place_id' => 6, 'distance' => 2880, 'obstacle_width' => 360, 'ss_count' => 0, 'tasuki' => 0, 's_level' => 21],
    215 => ['corse_id' => 215, 'place_id' => 3, 'distance' => 3350, 'obstacle_width' => 372, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 19],
    166 => ['corse_id' => 166, 'place_id' => 6, 'distance' => 4100, 'obstacle_width' => 373, 'ss_count' => 0, 'tasuki' => 1, 's_level' => 35],
    163 => ['corse_id' => 163, 'place_id' => 3, 'distance' => 3380, 'obstacle_width' => 376, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 18],
    51  => ['corse_id' => 51, 'place_id' => 3, 'distance' => 2750, 'obstacle_width' => 393, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 15],
    54  => ['corse_id' => 54, 'place_id' => 3, 'distance' => 2770, 'obstacle_width' => 396, 'ss_count' => 1, 'tasuki' => 1, 's_level' => 15],
    279 => ['corse_id' => 279, 'place_id' => 7, 'distance' => 3900, 'obstacle_width' => 254, 'ss_count' => 2, 'tasuki' => 0, 's_level' => 25],
  ];
}
