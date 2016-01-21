import simplejson as json
import urllib
import MySQLdb

urllib.urlopen('http://127.0.0.1/api/superkg/index.php')
jsonurl = 'http://127.0.0.1/api/superkg/result.json'
jsondata = json.loads(urllib.urlopen(jsonurl).read())

con = MySQLdb.connect(host='127.0.0.1', user='nota', passwd='nota', db='nota')
cur = con.cursor()

con.set_character_set('utf8')
cur.execute('SET NAMES utf8;')
cur.execute('SET CHARACTER SET utf8;')
cur.execute('SET character_set_connection=utf8;')


for song in jsondata['list']:
    artists = ' & '.join(song['artist']).encode('utf-8')
    title = song['song'].encode('utf-8')
    id = song['id'].encode('utf-8')

    try:
        cur.callproc('import_song', (id, title, artists))
    except MySQLdb.Error, e:
        print "MySQL Error [%d]: %s" % (e.args[0], e.args[1])
    except IndexError:
        print "MySQL Error: %s" % str(e)

cur.close()
con.close()
