#!/bin/bash
SHELL=/bin/bash

cd /var/www/infoscreen

pkill "python scs_website/get_articles.py"
pkill "python scs_website/get_images.py"
pkill "python twitter/twitter.py"
pkill "phantomjs*"
pkill "curl*"

if [ `stat --format=%Y weather/windguru.png` -le $(( `date +%s` - 3700 )) ]; then 
    phantomjs weather/rasterize.js http://localhost/infoscreen/weather/windguru.html weather/windguru.png "entire page" 2 "tabid_0_content_div"
fi

if [ `stat --format=%Y weather/bodenanalyse_europa.jpg` -le $(( `date +%s` - 3700 )) ]; then 
	curl "http://thumb.sc-schwielochsee.de/?format=jpg&width=1020&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/KU/KUPK/Hobbymet/Wetterkarten/Analysekarten/Analysekarten__Default__Boden__Europa__Luftdruck__Bild,property=default.png" -o weather/bodenanalyse_europa.jpg
fi

if [ `stat --format=%Y weather/radar_deutschland.jpg` -le $(( `date +%s` - 3700 )) ]; then 
	curl "http://thumb.sc-schwielochsee.de/?format=jpg&height=653&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/WV/WVFK/Dynamisches/Regional/noaaDeutAktuell,templateId=poster,property=default.jpg" -o weather/radar_deutschland.jpg
fi

if [ `stat --format=%Y weather/prognose_48h.jpg` -le $(( `date +%s` - 3700 )) ]; then 
	curl "http://thumb.sc-schwielochsee.de/?format=jpg&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/KU/KUPK/Hobbymet/Wetterkarten/Kurzfrist-Prognosekarten/KFprogkarte00Z__Default__TKB__H_2B48__Europa__Bild,property=default.jpg" -o weather/prognose_48h.jpg
fi

source /usr/local/bin/virtualenvwrapper.sh
workon python34

if [ `stat --format=%Y twitter.touch` -le $(( `date +%s` - 3700 )) ]; then 
	python twitter/twitter.py
	touch twitter.touch
fi

if [ `stat --format=%Y scs_articles.touch` -le $(( `date +%s` - 3700 )) ]; then 
	python scs_website/get_articles.py
	touch scs_articles.touch
fi

if [ `stat --format=%Y scs_images.touch` -le $(( `date +%s` - 3700 )) ]; then 
	python scs_website/get_images.py
	touch scs_images.touch
fi

deactivate
