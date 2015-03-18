import feedparser
import requests
import shutil
import hashlib
import json
import re
from pathlib import Path
import sqlite3 as s3
import os
from urlpath import URL

r = requests.get("https://sc-schwielochsee.de/index.php?option=com_ninjarsssyndicator&feed_id=1&format=raw")
feed = feedparser.parse( r.text )
del r

ozio_pattern = re.compile("{oziogallery __231__(\d+)}")
widgetkit_pattern = re.compile("\[widgetkit id=(\d+)\]")

con = s3.connect(str(((Path(__file__).resolve() / '..' / '..').resolve() / 'news.sqlite').resolve()))
with con:
	cur = con.cursor()
	cur.execute("SELECT id, url, path FROM downloads ORDER BY priority")

	for result in cur.fetchall():
		print(result[0])
		file_path = Path("../").resolve() / result[2]

		f = file_path.open("wb")
		
		f.write(requests.get(result[1]).content)

		f.close()

		cur.execute("DELETE FROM downloads WHERE id = ?", (result[0], ))
		con.commit()