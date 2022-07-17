echo 'Search: '
read tgt_format

john --list=formats | tr " " "\n" | grep -iF "$tgt_format"
