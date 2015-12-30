#!/bin/bash

rm -rf tmp
mkdir tmp

for i in lumen/app/Http; do
cp -r $i "tmp/$(dirname $(dirname $i))"
done

apidoc -i tmp -o doc
rm -r tmp
