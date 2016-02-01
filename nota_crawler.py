import simplejson as json
import urllib
import MySQLdb

urllib.urlopen('http://127.0.0.1/api/superkg/index.php')
jsonurl = 'http://127.0.0.1/api/superkg/result.json'
json_string = urllib.urlopen(jsonurl).read()
json_data = json.loads(json_string)

#import_song

con = MySQLdb.connect(host='127.0.0.1', user='root', passwd='Not@db', db='nota')
cur = con.cursor()

con.set_character_set('utf8')
cur.execute('SET NAMES utf8;')
cur.execute('SET CHARACTER SET utf8;')
cur.execute('SET character_set_connection=utf8;')


for song in json_data['list']:
    song_url = 'http://media.super.kg/media/audio/a_' + song['id'] + '.mp3'
    song_url = song_url.encode('utf-8')
    for artist_in in song['artist']:
        artist = artist_in.encode('utf-8')
        song_title = song['song'].encode('utf-8')
        print(artist + ' ' + song_title)
        try:
            cur.execute("select id from vass_artists where name = %s;", (artist,))
            artist_id = cur.fetchone()
            if artist_id is None:
                cur.execute("insert into vass_artists (name, tag, bio, follow, link) values(%s, '', %s, 0, '');", (artist,artist))
                artist_id = int(cur.lastrowid)
            else:
                artist_id = artist_id[0]
            cur.execute("select count(1) from vass_songs where url = %s and artist_id = %s;", (song_url, artist_id))
            if cur.fetchone()[0] == 0:
                cur.execute("""\
                insert into vass_songs(recent, song_country, user_id, created_on, url, artist_id, title, album_id, tags, lyrics, artists_id, last_loved, fav, played, purchase_url, permalink_url)
                values(1, 116, 1, sysdate(), %s, %s, %s, 0, %s, '', '', sysdate(), 0, 0, '', '');
                            """,(song_url, artist_id, song_title, song_title)
                )

        except MySQLdb.Error, e:
            print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
        except IndexError:
            print "MySQL Error: %s" % str(e)

cur.close()
con.close()
