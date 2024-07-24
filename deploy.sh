#!/bin/bash

# Add all changes
git add .

# Commit changes with a message
commit_message="Auto commit"
if [ -n "$1" ]; then
    commit_message="$1"
fi

git commit -m "$commit_message"

# Check if the commit was successful
if [ $? -eq 0 ]; then
    echo "Successfully committed changes"
    
    # Push to the main branch on origin
    git push origin main
    
    # Check if the first push was successful
    if [ $? -eq 0 ]; then
        echo "Successfully pushed to origin main"
        
        # Push to the main branch on production
        git push production main
        
        # Check if the second push was successful
        if [ $? -eq 0 ]; then
            echo "Successfully pushed to production main"
        else
            echo "Failed to push to production main"
            exit 1
        fi
    else
        echo "Failed to push to origin main"
        exit 1
    fi
else
    echo "Failed to commit changes"
    exit 1
fi
