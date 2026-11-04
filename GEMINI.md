# Project Guidelines & Codebase Intelligence

This project uses `codebase-memory-mcp` for high-performance code intelligence and token optimization.

## Token Saving Guidelines
- **Architecture Overview**: Use `get_architecture` (or CLI `codebase-memory-mcp cli get_architecture`) instead of manually scanning directory trees.
- **Symbol Discovery**: Use `search_graph` to find models, Filament resources, policies, and methods with exact line numbers and paths.
- **Targeted Code Reading**: Use `get_code_snippet` to inspect specific functions/methods with minimal tokens rather than loading entire multi-hundred line files.
- **Dependency Tracing**: Use `trace_path` to explore caller/callee graphs and relations between models, services, and Filament pages.
- **Keep Index Updated**: The index automatically refreshes on session start; incremental updates run via `index_repository`.
