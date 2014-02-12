{placemark_list}

	var placemark{id} = new YMaps.Placemark(new YMaps.GeoPoint({latitude}, {longitude}), { balloonOptions :{ maxWidth:250, maxHeight:180}});
	placemark{id}.setIconContent('{title}');
	placemark{id}.setBalloonContent('{note}');
	map{map_id}.addOverlay(placemark{id});
	
{/placemark_list}