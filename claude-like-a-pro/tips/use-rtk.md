**Cut Claude Code's token usage by 70% with rtk**

Most context is noise — passing tests, progress bars, verbose logs. `rtk` is a CLI proxy that compresses command output before it hits the window.

```jsx
# Requires Rust
cargo install --git https://github.com/rtk-ai/rtk

# Wire into Claude Code
rtk init --global

# Check savings any time:
rtk gain
```
