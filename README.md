# Snewker

Snewker is a terminal application that uses the WST APIs to show the latest snooker data.

## Installation

Clone this repository and run:  
`composer install`  
`php snewker migrate`

## Usage

`php snewker matches` - Show the latest matches of the current tournament.  
`php snewker rankings` - Show the latest rankings of the current 2-year World Rankings.  
`php snewker favourite-players` - Add players to your favourites to get desktop notifications when they're playing and when a match ends.  

On MacOS you can use the following command to fetch matches every minute:  
`while true; do php snewker matches; sleep 60; done`
