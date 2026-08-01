**use loop to Monitor background task without leaving Claude Code**

`/loop 5m check if the deploy succeeded` runs a recurring prompt in the background while you stay focused on real work.

```jsx
/loop 5m check if the deploy succeeded and report back
/loop 20m /review-pr 1234
/loop check the build status   # defaults to 10m
```
