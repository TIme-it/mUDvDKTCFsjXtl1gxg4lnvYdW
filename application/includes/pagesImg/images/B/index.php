<?PHP

  include_once('../../../config.php');
  include_once(_Path.'_topBD.php');
  $Site->Title = 'Поиск по сайту';
  $Site->PageTitle = ' '.TITLE_DELIM.' '.$Site->Title;
  include_once(_Path.'_topMain.php');

  include_once(_Path.'_bottomMain.php');
  include_once(_Path.'_bottomBD.php');
?>