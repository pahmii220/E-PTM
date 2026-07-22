import urllib.request
import json
import time

subdistricts = [
    "Banjarmasin Utara",
    "Banjarmasin Barat",
    "Banjarmasin Tengah",
    "Banjarmasin Timur",
    "Banjarmasin Selatan"
]

features = []

for sd in subdistricts:
    query = urllib.parse.quote(f"{sd} Banjarmasin")
    url = f"https://nominatim.openstreetmap.org/search.php?q={query}&polygon_geojson=1&format=json"
    print(f"Fetching {sd}...")
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'})
    try:
        with urllib.request.urlopen(req) as response:
            data = json.loads(response.read().decode('utf-8'))
            if data:
                # Get the first result which is usually the administrative boundary
                # Look for class="boundary" and type="administrative"
                valid_result = None
                for res in data:
                    if res.get('class') == 'boundary' and res.get('type') == 'administrative':
                        valid_result = res
                        break
                
                if not valid_result:
                    valid_result = data[0] # Fallback
                
                geojson = valid_result.get('geojson')
                if geojson:
                    feature = {
                        "type": "Feature",
                        "properties": {
                            "name": sd,
                            "display_name": valid_result.get('display_name')
                        },
                        "geometry": geojson
                    }
                    features.append(feature)
        time.sleep(1) # Be polite to Nominatim
    except Exception as e:
        print(f"Failed for {sd}: {e}")

feature_collection = {
    "type": "FeatureCollection",
    "features": features
}

with open('public/geojson_banjarmasin.json', 'w') as f:
    json.dump(feature_collection, f)

print("Done! Saved to public/geojson_banjarmasin.json")
