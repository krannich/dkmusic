# dkmusic :: the ultimate mp3 music library manager.
dkmusic is the ultimate mp3 music library manager built on the PHP framework Laravel. It is intended to organize your mp3 music collection by preventing duplicates.

## How does it work?

### Import
Your music files will be organized in the corresponding folders (#, A-Z) of your music library.
Duplicates will be removed according to their acoustid and audio fingerprint.
For more information about acoustid and audio fingerprints visit www.acoustid.org.

**The following steps are performed:**
- Remove punctuation marks, apostrophes, curly and square brackets from artist and title
- Remove "The" and "Die" from beginning of artist
- Replace german umlauts, accents, and special characters.
- Convert "featuring" and "feat" to "ft"
- Convert artist and title to lowercase except first letter
- Rename filename accordingly (a timestamp is added if filename already exists)

**Afterwards your music files will look like this:**
- Phil collins - In the air tonight (radio edit).mp3
- Flo rida ft akon - Who dat girl (mds dont know who she is remix).mp3
- Howard carpendale - Nachts wenn alles schlaeft.mp3
- Black eyed peas - Dont stop the party.mp3

**Note:** Since the external library getID3 does not support to ID3-tag writing of m4a files,
all your m4a files must to be converted to mp3.


## Contributing to dkmusic
Contributions are encouraged and welcome!


## Thanks
To all the beta testers! Without you it wouldn't be possible to develop great software like this.


## Licenses
dkmusic is open-sourced software licensed under the MIT License.

dkmusic uses/requires the following third-party libraries:
- Laravel licensed under the MIT License.
- jQuery licensed under the MIT License.
- Underscore.js licensed under the MIT License.
- Backbone licensed under the MIT License.
- Mustache.js licensed under the MIT License.
- Twitter Bootstrap licensed under Apache License Version 2.0.
- Font Awesome licensed under the SIL Open Font License.
- FFMPEG licensed under the GNU Lesser General Public License (LGPL) version 2.1 or later.
- FPCalc licensed under the GNU Lesser General Public License (LGPL) version 2.1 or later.
