# How to use the WordPress Cron and Action Scheduler to automate WordPress tasks.
## Search and import Jamendo playlists and schedule their tracks data import.

This repository is a basic WordPress plugin created to illustrate a tutorial about WordPress automated tasks using Cron jobs and the Action Scheduler library.

Its main function is to:
- fetch the latest 10 Jamendo playlists containing the "electro" keyword via a Cron job,
- save these playlists as (CPT) posts,
- fetch tracks belonging to imported playlist using an asynchronous tasks with Action Scheduler,
- alter single post content to display the playlist data.

[Read the blog post →](https://mosaika.fr/cron-wordpress-action-scheduler/)