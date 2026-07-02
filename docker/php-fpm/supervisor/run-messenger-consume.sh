#! /usr/bin/env bash

# Run messenger
bin/console messenger:consume default scheduler_default -vv --limit=10 --time-limit=1800
