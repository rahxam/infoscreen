#!/usr/bin/env python3

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
iframe_pattern = re.compile("<iframe.*</iframe>")
image_pattern = re.compile("<img(.*)src=\"(.*)\"([^>]*)>")

con = s3.connect(str(((Path(__file__).resolve() / '..' / '..').resolve() / 'news.sqlite').resolve()))
with con:
	cur = con.cursor()
	for item in feed.entries:
		cur.execute("SELECT date FROM news WHERE foreign_id = ?",  (item['id'], ))
		result = cur.fetchone() 

		print(item['updated'])

		if result and result[0] != item['updated']:
			cur.execute("DELETE FROM news WHERE foreign_id = ?",  (item['id'], ))
			result = None
			print('modified')

		if not result:
			print('add')

			r = requests.get(item['id'], params={'format' : 'json'})
			article = r.json()
			article['images'] = json.loads(article['images'])

			if len(article['images']['image_fulltext']) > 0 :
				path = Path("scs_website/files/articles/intro/" + hashlib.md5(str(article['images']['image_fulltext']).encode('utf-8')).hexdigest() + Path(article['images']['image_fulltext']).suffix).as_posix()
			
				cur.execute("INSERT INTO downloads (priority, url, path) VALUES (1, ?, ?)", ("https://sc-schwielochsee.de/" + article['images']['image_fulltext'], path))
				print(article['images']['image_fulltext'])
			else:
				path = ""

			match_ozio = ozio_pattern.search(article['fulltext'])
			match_image = image_pattern.findall(article['fulltext'])

			article['fulltext'] = re.sub(ozio_pattern, '', article['fulltext'])
			article['fulltext'] = re.sub(widgetkit_pattern, '', article['fulltext'])
			article['fulltext'] = re.sub(iframe_pattern, '', article['fulltext'])
			
			if match_ozio:
				google_album = match_ozio.group(1)

				dir = (Path(__file__).resolve() / '..' / "files" / "articles").resolve() / google_album

				os.makedirs(str(dir), exist_ok=True)

				picasa_r = requests.get("http://picasaweb.google.com/data/feed/api/user/SCSchwielochsee/albumid/" + google_album, params={'alt' : 'json'})
				picasa = picasa_r.json()

				counter = len(picasa['feed']['entry']) - 1
				if(counter > 5):
					counter = 5
				
				for i in range(0, counter):
					img_url = URL(picasa['feed']['entry'][i]['media$group']['media$content'][0]['url'])
					img_url = img_url.parents[0] / 's900' / img_url.name
					img_path = Path("scs_website/files/articles/" + google_album + "/" + hashlib.md5(str(img_url).encode('utf-8')).hexdigest() + img_url.suffix)

					cur.execute("INSERT INTO downloads (priority, url, path) VALUES (5, ?, ?)", (str(img_url), img_path.as_posix()))
					print(img_url)
			else:
				google_album = ""

			if match_image:
				for match in match_image:
					id_hash = hashlib.md5(str(item['id']).encode('utf-8')).hexdigest()
					dir = (Path(__file__).resolve() / '..' / "files" / "articles").resolve() / id_hash

					os.makedirs(str(dir), exist_ok=True)

					src = URL(match[1])
					img_path = Path("scs_website/files/articles/" + id_hash + "/" + hashlib.md5(str(src).encode('utf-8')).hexdigest() + src.suffix)

					cur.execute("INSERT INTO downloads (priority, url, path) VALUES (3, ?, ?)", (str(src), img_path.as_posix()))

					article['fulltext'] = article['fulltext'].replace(match[1], img_path.as_posix())

					print(str(src))


			cur.execute("INSERT INTO news (foreign_id, date, published, text, title, image, author, google_album, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'article')", (item['id'], item['updated'], article['publish_up'], article['fulltext'], article['title'], path, article['created_by_alias'], google_album))
			con.commit()
