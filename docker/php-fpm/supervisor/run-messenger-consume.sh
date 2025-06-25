#! /usr/bin/env bash

# Run messenger
bin/console messenger:consume default -vv --limit=10 --time-limit=1800
