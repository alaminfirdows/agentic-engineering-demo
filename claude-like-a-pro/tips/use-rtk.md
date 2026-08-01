# Cut Claude Code's token usage by 70% with rtk

Most context is noise — passing tests, progress bars, verbose logs. `rtk` is a CLI proxy that compresses command output before it hits the window.

## Installation

Requires Rust:

```bash
cargo install --git https://github.com/rtk-ai/rtk
```

## Setup

Wire into Claude Code:

```bash
rtk init --global
```

## Monitor savings

Check token savings any time:

```bash
rtk gain
```

## Benefit

Dramatically reduces context waste, freeing up window space for actual code and logic instead of command noise.
