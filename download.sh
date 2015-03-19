#!/bin/bash
cd /var/www/infoscreen

phantomjs rasterize.js http://localhost/infoscreen/weather/windguru.html weather/windguru.png "entire page" 2 "tabid_0_content_div"

curl "http://thumb.sc-schwielochsee.de/?format=jpg&width=1020&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/KU/KUPK/Hobbymet/Wetterkarten/Analysekarten/Analysekarten__Default__Boden__Europa__Luftdruck__Bild,property=default.png" -o weather/bodenanalyse_europa.jpg
curl "http://thumb.sc-schwielochsee.de/?format=jpg&height=653&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/WV/WVFK/Dynamisches/Regional/noaaDeutAktuell,templateId=poster,property=default.jpg" -o weather/radar_deutschland.jpg
curl "http://thumb.sc-schwielochsee.de/?format=jpg&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/KU/KUPK/Hobbymet/Wetterkarten/Kurzfrist-Prognosekarten/KFprogkarte00Z__Default__TKB__H_2B48__Europa__Bild,property=default.jpg" -o weather/prognose_48h.jpg

source `which virtualenvwrapper.sh`
workon python34
python twitter/twitter.py
python scs_website/get_articles.py
python scs_website/get_images.py
deactivate