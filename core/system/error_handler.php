<?php

	function handleException($e) {
		echo '<b>Code:</b> '.$e->getCode()."<br>";
		echo '<p style="width:90%; padding: 0.5em; color:red;"><b>Message:</b> '.$e->getMessage()."</p>";

		$trace = $e->getTrace();

		echo '<ol>';

		foreach($trace as $number => $frame) {
			echo '<li><a href="#f'.$number.'">'.$frame['class'].'::'.$frame['function'].'()</a></li>';
		}

		echo '</ol>';
		foreach($trace as $number => $frame) {
			echo '<a name="f'.$number.'"></a>';
			echo '<div style="border:dotted 1px black; margin-bottom: 1em;">';
			echo '<p>';
			echo '<span style="color:green">'.$frame['file'].' ['.$frame['line'].']</span> ';
			echo $frame['class'].'::'.$frame['function'].'(';

			$is_first = true;

			foreach($frame['args'] as $anumber => $arg) {
				if($is_first)
					$is_first = false;
				else
					echo ', ';

				echo '<a href="#f'.$number.'a'.$anumber.'">';
				$type = gettype($arg);

				if($type == 'object') {
					echo get_class($arg);
				} else {
					echo $type;
				}

				echo '</a>';
			}

			echo ')</p>';

			foreach($frame['args'] as $anumber => $arg) {
				echo '<a name="f'.$number.'a'.$anumber.'"></a>';
				echo '<pre style="padding:0.4em; background:rgb(240,240,240); margin-left: 1em;">';
				var_dump($arg);
				echo '</pre>';
			}

			echo '</div>';
		}

	die();
}