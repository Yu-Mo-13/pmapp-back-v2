#!/bin/bash

# ReviewDog wrapper script for safe execution
# Usage: reviewdog-wrapper.sh <format> <name> <reporter> <level> [input-file]

set -e

FORMAT="$1"
NAME="$2"
REPORTER="$3"
LEVEL="$4"
INPUT_FILE="${5:-/dev/stdin}"

# Check if input file exists and has content
if [ "$INPUT_FILE" != "/dev/stdin" ] && [ ! -s "$INPUT_FILE" ]; then
    echo "No issues found for $NAME"
    exit 0
fi

# Check if stdin has content (when using pipe)
if [ "$INPUT_FILE" = "/dev/stdin" ]; then
    # Read stdin to temporary file to check if it has content
    TEMP_FILE=$(mktemp)
    cat > "$TEMP_FILE"
    if [ ! -s "$TEMP_FILE" ]; then
        echo "No issues found for $NAME"
        rm "$TEMP_FILE"
        exit 0
    fi
    INPUT_FILE="$TEMP_FILE"
fi

# Execute ReviewDog
reviewdog -f="$FORMAT" -name="$NAME" -reporter="$REPORTER" -level="$LEVEL" < "$INPUT_FILE"

# Cleanup temporary file if created
if [[ "$INPUT_FILE" == /tmp/tmp.* ]]; then
    rm "$INPUT_FILE"
fi
