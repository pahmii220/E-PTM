import urllib.request
import urllib.parse
import json

overpass_url = "http://overpass-api.de/api/interpreter"
overpass_query = """
[out:json];
area["name"="Banjarmasin"]["admin_level"="6"]->.searchArea;
(
  relation["admin_level"="7"](area.searchArea);
);
out geom;
"""
try:
    data = urllib.parse.urlencode({'data': overpass_query}).encode('utf-8')
    req = urllib.request.Request(overpass_url, data=data, headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Accept': 'application/json'})
    with urllib.request.urlopen(req) as response:
        result = json.loads(response.read().decode('utf-8'))
        
        # Build simple GeoJSON
        features = []
        for element in result.get('elements', []):
            if element['type'] == 'relation':
                name = element.get('tags', {}).get('name', 'Unknown')
                # Find the outer way
                coords = []
                for member in element.get('members', []):
                    if member['type'] == 'way' and member.get('role') == 'outer':
                        way_coords = [[pt['lon'], pt['lat']] for pt in member.get('geometry', [])]
                        if way_coords:
                            coords.append(way_coords)
                
                if coords:
                    feature = {
                        "type": "Feature",
                        "properties": {"name": name},
                        "geometry": {
                            "type": "Polygon" if len(coords) == 1 else "MultiPolygon",
                            "coordinates": [coords] if len(coords) == 1 else [coords]
                        }
                    }
                    features.append(feature)
        
        geojson = {
            "type": "FeatureCollection",
            "features": features
        }
        with open('public/geojson_banjarmasin.json', 'w') as f:
            json.dump(geojson, f)
        print("Success! Saved to public/geojson_banjarmasin.json")
except Exception as e:
    import traceback
    traceback.print_exc()
