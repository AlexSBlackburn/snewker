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

The matches command will fetch the latest matches every minute until cancelled.


## To do
- Fix order of rounds (Round 1, Round 2, Quarter Finals, Semi Finals, Final).
- Write tests
