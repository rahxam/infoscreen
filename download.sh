#!/bin/bash
SHELL=/bin/bash

cd /var/www/infoscreen

source /usr/local/bin/virtualenvwrapper.sh
workon python34

pkill "python twitter/twitter.py"

if [ `stat --format=%Y twitter.touch` -le $(( `date +%s` - 3700 )) ]; then 
	python twitter/twitter.py
	touch twitter.touch
fi

pkill "python scs_website/get_articles.py"

if [ `stat --format=%Y scs_articles.touch` -le $(( `date +%s` - 3700 )) ]; then 
	python scs_website/get_articles.py
	touch scs_articles.touch
fi

pkill "phantomjs*"

if [ `stat --format=%Y weather/windguru.png` -le $(( `date +%s` - 3700 )) ]; then 
    phantomjs weather/rasterize.js http://localhost/infoscreen/weather/windguru.html weather/windguru.png "entire page" 2 "tabid_0_content_div"
fi

pkill "curl*"

if [ `stat --format=%Y weather/bodenanalyse_europa.jpg` -le $(( `date +%s` - 14300 )) ]; then 
	curl "http://thumb.sc-schwielochsee.de/?format=jpg&width=1020&url=http://www.dwd.de/DWD/wetter/wv_spez/hobbymet/wetterkarten/bwk_bodendruck_na_ana.png" -o weather/bodenanalyse_europa.jpg
fi

if [ `stat --format=%Y weather/radar_deutschland.jpg` -le $(( `date +%s` - 3700 )) ]; then 
	curl "http://thumb.sc-schwielochsee.de/?format=jpg&height=653&url=http://www.dwd.de/DWD/wetter/radar/rad_brd_akt.jpg" -o weather/radar_deutschland.jpg
fi

if [ `stat --format=%Y weather/prognose_48h.jpg` -le $(( `date +%s` - 14300 )) ]; then 
	curl "http://thumb.sc-schwielochsee.de/?format=jpg&url=http://www.dwd.de/DWD/wetter/wv_allg/europa/bilder/vhs_euro_uebermorgen.jpg" -o weather/prognose_48h.jpg
fi

pkill "curl*"

if [ `stat --format=%Y scs_images.touch` -le $(( `date +%s` - 3700 )) ]; then 
	python scs_website/get_images.py
	touch scs_images.touch
fi

deactivate
