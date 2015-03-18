#!/bin/bash
wget http://thumb.sc-schwielochsee.de/?format=jpg&width=1020&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/KU/KUPK/Hobbymet/Wetterkarten/Analysekarten/Analysekarten__Default__Boden__Europa__Luftdruck__Bild,property=default.png bodenanalyse_europa.jpg
wget http://thumb.sc-schwielochsee.de/?format=jpg&height=653&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/WV/WVFK/Dynamisches/Regional/noaaDeutAktuell,templateId=poster,property=default.jpg radar_deutschland.jpg
wget http://thumb.sc-schwielochsee.de/?format=jpg&url=http://www.dwd.de/bvbw/generator/DWDWWW/Content/Oeffentlichkeit/KU/KUPK/Hobbymet/Wetterkarten/Kurzfrist-Prognosekarten/KFprogkarte00Z__Default__TKB__H_2B48__Europa__Bild,property=default.jpg prognose_48h.jpg


python3 twitter/twitter.py
python3 scs_website/get_articles.py
python3 scs_website/get_images.py