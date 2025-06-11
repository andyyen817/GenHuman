// vite.config.ts
import { URL, fileURLToPath } from "node:url";
import path from "node:path";
import { defineConfig, loadEnv } from "file:///E:/vue/digitalhuman-admin/node_modules/vite/dist/node/index.js";
import vue from "file:///E:/vue/digitalhuman-admin/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vueJsx from "file:///E:/vue/digitalhuman-admin/node_modules/@vitejs/plugin-vue-jsx/dist/index.mjs";
import AutoImport from "file:///E:/vue/digitalhuman-admin/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///E:/vue/digitalhuman-admin/node_modules/unplugin-vue-components/dist/vite.mjs";
import { createSvgIconsPlugin } from "file:///E:/vue/digitalhuman-admin/node_modules/vite-plugin-svg-icons/dist/index.mjs";
import { viteMockServe } from "file:///E:/vue/digitalhuman-admin/node_modules/vite-plugin-mock/dist/index.js";
var __vite_injected_original_import_meta_url = "file:///E:/vue/digitalhuman-admin/vite.config.ts";
var vite_config_default = defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd());
  return {
    // 开发或生产环境服务的公共基础路径
    base: env.VITE_BASE,
    // 路径别名
    resolve: {
      alias: {
        "~": fileURLToPath(new URL("./", __vite_injected_original_import_meta_url)),
        "@": fileURLToPath(new URL("./src", __vite_injected_original_import_meta_url))
      }
    },
    // 引入sass全局样式变量
    css: {
      preprocessorOptions: {
        scss: {
          additionalData: `@import "@/styles/var.scss";`
        }
      }
    },
    server: {
      // 服务启动时是否自动打开浏览器
      open: false,
      // 本地跨域代理 -> 代理到服务器的接口地址
      proxy: {
        // '/api': {
        "/admin": {
          target: env.VITE_API_BASE_URL,
          // 后台服务器地址
          changeOrigin: true,
          // 是否允许不同源
          secure: false,
          // 支持https
          rewrite: (path2) => path2.replace(/^\/api/, "/api")
        }
      }
    },
    plugins: [
      vue(),
      vueJsx(),
      AutoImport({
        // 自动导入vue相关函数，如: ref、reactive、toRef等
        imports: ["vue", "vue-router", {
          // vue 3.5.x
          vue: ["useTemplateRef", "onWatcherCleanup", "useId"]
        }],
        dts: "src/auto-import.d.ts"
      }),
      Components({
        // 指定组件位置，默认是 src/components 自动导入自定义组件
        dirs: ["src/components"],
        extensions: ["vue", "tsx"],
        // 配置文件生成位置
        dts: "src/components.d.ts"
      }),
      createSvgIconsPlugin({
        // 指定需要缓存的图标文件夹
        iconDirs: [path.resolve(process.cwd(), "src/icons")],
        // 指定symbolId格式
        symbolId: "icon-[dir]-[name]"
      }),
      viteMockServe({
        mockPath: "mock",
        // 目录位置
        logger: true,
        //  是否在控制台显示请求日志
        supportTs: true,
        // 是否读取ts文件模块
        // localEnabled: true, // 设置是否启用本地mock文件
        localEnabled: false,
        // 设置是否启用本地mock文件
        prodEnabled: true,
        // 设置打包是否启用mock功能
        // 这样可以控制关闭mock的时候不让mock打包到最终代码内
        injectCode: `
          import { setupProdMockServer } from '../mock/index';
          setupProdMockServer();
        `
      })
    ],
    // 构建
    build: {
      chunkSizeWarningLimit: 2e3,
      // 消除打包大小超过500kb警告
      outDir: "dist",
      // 指定打包路径，默认为项目根目录下的dist目录
      minify: "terser",
      // Vite 2.6.x 以上需要配置 minify："terser"，terserOptions才能生效
      terserOptions: {
        compress: {
          keep_infinity: true,
          // 防止 Infinity 被压缩成 1/0，这可能会导致 Chrome 上的性能问题
          drop_console: true,
          // 生产环境去除 console
          drop_debugger: true
          // 生产环境去除 debugger
        },
        format: {
          comments: false
          // 删除注释
        }
      },
      // 静态资源打包到dist下的不同目录
      rollupOptions: {
        output: {
          chunkFileNames: "static/js/[name]-[hash].js",
          entryFileNames: "static/js/[name]-[hash].js",
          assetFileNames: "static/[ext]/[name]-[hash].[ext]"
        }
      }
    }
  };
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJFOlxcXFx2dWVcXFxcZGlnaXRhbGh1bWFuLWFkbWluXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJFOlxcXFx2dWVcXFxcZGlnaXRhbGh1bWFuLWFkbWluXFxcXHZpdGUuY29uZmlnLnRzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9FOi92dWUvZGlnaXRhbGh1bWFuLWFkbWluL3ZpdGUuY29uZmlnLnRzXCI7aW1wb3J0IHsgVVJMLCBmaWxlVVJMVG9QYXRoIH0gZnJvbSAnbm9kZTp1cmwnXHJcbmltcG9ydCBwYXRoIGZyb20gJ25vZGU6cGF0aCdcclxuaW1wb3J0IHsgZGVmaW5lQ29uZmlnLCBsb2FkRW52IH0gZnJvbSAndml0ZSdcclxuaW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnXHJcbmltcG9ydCB2dWVKc3ggZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlLWpzeCdcclxuaW1wb3J0IEF1dG9JbXBvcnQgZnJvbSAndW5wbHVnaW4tYXV0by1pbXBvcnQvdml0ZSdcclxuaW1wb3J0IENvbXBvbmVudHMgZnJvbSAndW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZSdcclxuaW1wb3J0IHsgY3JlYXRlU3ZnSWNvbnNQbHVnaW4gfSBmcm9tICd2aXRlLXBsdWdpbi1zdmctaWNvbnMnXHJcbmltcG9ydCB7IHZpdGVNb2NrU2VydmUgfSBmcm9tICd2aXRlLXBsdWdpbi1tb2NrJ1xyXG5cclxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKCh7IG1vZGUgfSkgPT4ge1xyXG4gIGNvbnN0IGVudiA9IGxvYWRFbnYobW9kZSwgcHJvY2Vzcy5jd2QoKSkgYXMgSW1wb3J0TWV0YUVudlxyXG5cclxuICByZXR1cm4ge1xyXG4gICAgLy8gXHU1RjAwXHU1M0QxXHU2MjE2XHU3NTFGXHU0RUE3XHU3M0FGXHU1ODgzXHU2NzBEXHU1MkExXHU3Njg0XHU1MTZDXHU1MTcxXHU1N0ZBXHU3ODQwXHU4REVGXHU1Rjg0XHJcbiAgICBiYXNlOiBlbnYuVklURV9CQVNFLFxyXG4gICAgLy8gXHU4REVGXHU1Rjg0XHU1MjJCXHU1NDBEXHJcbiAgICByZXNvbHZlOiB7XHJcbiAgICAgIGFsaWFzOiB7XHJcbiAgICAgICAgJ34nOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vJywgaW1wb3J0Lm1ldGEudXJsKSksXHJcbiAgICAgICAgJ0AnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vc3JjJywgaW1wb3J0Lm1ldGEudXJsKSlcclxuICAgICAgfVxyXG4gICAgfSxcclxuICAgIC8vIFx1NUYxNVx1NTE2NXNhc3NcdTUxNjhcdTVDNDBcdTY4MzdcdTVGMEZcdTUzRDhcdTkxQ0ZcclxuICAgIGNzczoge1xyXG4gICAgICBwcmVwcm9jZXNzb3JPcHRpb25zOiB7XHJcbiAgICAgICAgc2Nzczoge1xyXG4gICAgICAgICAgYWRkaXRpb25hbERhdGE6IGBAaW1wb3J0IFwiQC9zdHlsZXMvdmFyLnNjc3NcIjtgXHJcbiAgICAgICAgfVxyXG4gICAgICB9XHJcbiAgICB9LFxyXG4gICAgc2VydmVyOiB7XHJcbiAgICAgIC8vIFx1NjcwRFx1NTJBMVx1NTQyRlx1NTJBOFx1NjVGNlx1NjYyRlx1NTQyNlx1ODFFQVx1NTJBOFx1NjI1M1x1NUYwMFx1NkQ0Rlx1ODlDOFx1NTY2OFxyXG4gICAgICBvcGVuOiBmYWxzZSxcclxuICAgICAgLy8gXHU2NzJDXHU1NzMwXHU4REU4XHU1N0RGXHU0RUUzXHU3NDA2IC0+IFx1NEVFM1x1NzQwNlx1NTIzMFx1NjcwRFx1NTJBMVx1NTY2OFx1NzY4NFx1NjNBNVx1NTNFM1x1NTczMFx1NTc0MFxyXG4gICAgICBwcm94eToge1xyXG4gICAgICAgIC8vICcvYXBpJzoge1xyXG4gICAgICAgICcvYWRtaW4nOiB7XHJcbiAgICAgICAgICB0YXJnZXQ6IGVudi5WSVRFX0FQSV9CQVNFX1VSTCwgLy8gXHU1NDBFXHU1M0YwXHU2NzBEXHU1MkExXHU1NjY4XHU1NzMwXHU1NzQwXHJcbiAgICAgICAgICBjaGFuZ2VPcmlnaW46IHRydWUsIC8vIFx1NjYyRlx1NTQyNlx1NTE0MVx1OEJCOFx1NEUwRFx1NTQwQ1x1NkU5MFxyXG4gICAgICAgICAgc2VjdXJlOiBmYWxzZSwgLy8gXHU2NTJGXHU2MzAxaHR0cHNcclxuICAgICAgICAgIHJld3JpdGU6IChwYXRoKSA9PiBwYXRoLnJlcGxhY2UoL15cXC9hcGkvLCAnL2FwaScpXHJcbiAgICAgICAgfVxyXG4gICAgICB9XHJcbiAgICB9LFxyXG4gICAgcGx1Z2luczogW1xyXG4gICAgICB2dWUoKSxcclxuICAgICAgdnVlSnN4KCksXHJcbiAgICAgIEF1dG9JbXBvcnQoe1xyXG4gICAgICAgIC8vIFx1ODFFQVx1NTJBOFx1NUJGQ1x1NTE2NXZ1ZVx1NzZGOFx1NTE3M1x1NTFGRFx1NjU3MFx1RkYwQ1x1NTk4MjogcmVmXHUzMDAxcmVhY3RpdmVcdTMwMDF0b1JlZlx1N0I0OVxyXG4gICAgICAgIGltcG9ydHM6IFsndnVlJywgJ3Z1ZS1yb3V0ZXInLCB7XHJcbiAgICAgICAgICAvLyB2dWUgMy41LnhcclxuICAgICAgICAgIHZ1ZTogWyd1c2VUZW1wbGF0ZVJlZicsICdvbldhdGNoZXJDbGVhbnVwJywgJ3VzZUlkJ11cclxuICAgICAgICB9XSxcclxuICAgICAgICBkdHM6ICdzcmMvYXV0by1pbXBvcnQuZC50cydcclxuICAgICAgfSksXHJcbiAgICAgIENvbXBvbmVudHMoe1xyXG4gICAgICAgIC8vIFx1NjMwN1x1NUI5QVx1N0VDNFx1NEVGNlx1NEY0RFx1N0Y2RVx1RkYwQ1x1OUVEOFx1OEJBNFx1NjYyRiBzcmMvY29tcG9uZW50cyBcdTgxRUFcdTUyQThcdTVCRkNcdTUxNjVcdTgxRUFcdTVCOUFcdTRFNDlcdTdFQzRcdTRFRjZcclxuICAgICAgICBkaXJzOiBbJ3NyYy9jb21wb25lbnRzJ10sXHJcbiAgICAgICAgZXh0ZW5zaW9uczogWyd2dWUnLCAndHN4J10sXHJcbiAgICAgICAgLy8gXHU5MTREXHU3RjZFXHU2NTg3XHU0RUY2XHU3NTFGXHU2MjEwXHU0RjREXHU3RjZFXHJcbiAgICAgICAgZHRzOiAnc3JjL2NvbXBvbmVudHMuZC50cydcclxuICAgICAgfSksXHJcbiAgICAgIGNyZWF0ZVN2Z0ljb25zUGx1Z2luKHtcclxuICAgICAgICAvLyBcdTYzMDdcdTVCOUFcdTk3MDBcdTg5ODFcdTdGMTNcdTVCNThcdTc2ODRcdTU2RkVcdTY4MDdcdTY1ODdcdTRFRjZcdTU5MzlcclxuICAgICAgICBpY29uRGlyczogW3BhdGgucmVzb2x2ZShwcm9jZXNzLmN3ZCgpLCAnc3JjL2ljb25zJyldLFxyXG4gICAgICAgIC8vIFx1NjMwN1x1NUI5QXN5bWJvbElkXHU2ODNDXHU1RjBGXHJcbiAgICAgICAgc3ltYm9sSWQ6ICdpY29uLVtkaXJdLVtuYW1lXSdcclxuICAgICAgfSksXHJcbiAgICAgIHZpdGVNb2NrU2VydmUoe1xyXG4gICAgICAgIG1vY2tQYXRoOiAnbW9jaycsIC8vIFx1NzZFRVx1NUY1NVx1NEY0RFx1N0Y2RVxyXG4gICAgICAgIGxvZ2dlcjogdHJ1ZSwgLy8gIFx1NjYyRlx1NTQyNlx1NTcyOFx1NjNBN1x1NTIzNlx1NTNGMFx1NjYzRVx1NzkzQVx1OEJGN1x1NkM0Mlx1NjVFNVx1NUZEN1xyXG4gICAgICAgIHN1cHBvcnRUczogdHJ1ZSwgLy8gXHU2NjJGXHU1NDI2XHU4QkZCXHU1M0Q2dHNcdTY1ODdcdTRFRjZcdTZBMjFcdTU3NTdcclxuICAgICAgICAvLyBsb2NhbEVuYWJsZWQ6IHRydWUsIC8vIFx1OEJCRVx1N0Y2RVx1NjYyRlx1NTQyNlx1NTQyRlx1NzUyOFx1NjcyQ1x1NTczMG1vY2tcdTY1ODdcdTRFRjZcclxuICAgICAgICBsb2NhbEVuYWJsZWQ6IGZhbHNlLCAvLyBcdThCQkVcdTdGNkVcdTY2MkZcdTU0MjZcdTU0MkZcdTc1MjhcdTY3MkNcdTU3MzBtb2NrXHU2NTg3XHU0RUY2XHJcbiAgICAgICAgcHJvZEVuYWJsZWQ6IHRydWUsIC8vIFx1OEJCRVx1N0Y2RVx1NjI1M1x1NTMwNVx1NjYyRlx1NTQyNlx1NTQyRlx1NzUyOG1vY2tcdTUyOUZcdTgwRkRcclxuICAgICAgICAvLyBcdThGRDlcdTY4MzdcdTUzRUZcdTRFRTVcdTYzQTdcdTUyMzZcdTUxNzNcdTk1RURtb2NrXHU3Njg0XHU2NUY2XHU1MDE5XHU0RTBEXHU4QkE5bW9ja1x1NjI1M1x1NTMwNVx1NTIzMFx1NjcwMFx1N0VDOFx1NEVFM1x1NzgwMVx1NTE4NVxyXG4gICAgICAgIGluamVjdENvZGU6IGBcclxuICAgICAgICAgIGltcG9ydCB7IHNldHVwUHJvZE1vY2tTZXJ2ZXIgfSBmcm9tICcuLi9tb2NrL2luZGV4JztcclxuICAgICAgICAgIHNldHVwUHJvZE1vY2tTZXJ2ZXIoKTtcclxuICAgICAgICBgXHJcbiAgICAgIH0pXHJcbiAgICBdLFxyXG4gICAgLy8gXHU2Nzg0XHU1RUZBXHJcbiAgICBidWlsZDoge1xyXG4gICAgICBjaHVua1NpemVXYXJuaW5nTGltaXQ6IDIwMDAsIC8vIFx1NkQ4OFx1OTY2NFx1NjI1M1x1NTMwNVx1NTkyN1x1NUMwRlx1OEQ4NVx1OEZDNzUwMGtiXHU4QjY2XHU1NDRBXHJcbiAgICAgIG91dERpcjogJ2Rpc3QnLCAvLyBcdTYzMDdcdTVCOUFcdTYyNTNcdTUzMDVcdThERUZcdTVGODRcdUZGMENcdTlFRDhcdThCQTRcdTRFM0FcdTk4NzlcdTc2RUVcdTY4MzlcdTc2RUVcdTVGNTVcdTRFMEJcdTc2ODRkaXN0XHU3NkVFXHU1RjU1XHJcbiAgICAgIG1pbmlmeTogJ3RlcnNlcicsIC8vIFZpdGUgMi42LnggXHU0RUU1XHU0RTBBXHU5NzAwXHU4OTgxXHU5MTREXHU3RjZFIG1pbmlmeVx1RkYxQVwidGVyc2VyXCJcdUZGMEN0ZXJzZXJPcHRpb25zXHU2MjREXHU4MEZEXHU3NTFGXHU2NTQ4XHJcbiAgICAgIHRlcnNlck9wdGlvbnM6IHtcclxuICAgICAgICBjb21wcmVzczoge1xyXG4gICAgICAgICAga2VlcF9pbmZpbml0eTogdHJ1ZSwgLy8gXHU5NjMyXHU2QjYyIEluZmluaXR5IFx1ODhBQlx1NTM4Qlx1N0YyOVx1NjIxMCAxLzBcdUZGMENcdThGRDlcdTUzRUZcdTgwRkRcdTRGMUFcdTVCRkNcdTgxRjQgQ2hyb21lIFx1NEUwQVx1NzY4NFx1NjAyN1x1ODBGRFx1OTVFRVx1OTg5OFxyXG4gICAgICAgICAgZHJvcF9jb25zb2xlOiB0cnVlLCAvLyBcdTc1MUZcdTRFQTdcdTczQUZcdTU4ODNcdTUzQkJcdTk2NjQgY29uc29sZVxyXG4gICAgICAgICAgZHJvcF9kZWJ1Z2dlcjogdHJ1ZSAvLyBcdTc1MUZcdTRFQTdcdTczQUZcdTU4ODNcdTUzQkJcdTk2NjQgZGVidWdnZXJcclxuICAgICAgICB9LFxyXG4gICAgICAgIGZvcm1hdDoge1xyXG4gICAgICAgICAgY29tbWVudHM6IGZhbHNlIC8vIFx1NTIyMFx1OTY2NFx1NkNFOFx1OTFDQVxyXG4gICAgICAgIH1cclxuICAgICAgfSxcclxuICAgICAgLy8gXHU5NzU5XHU2MDAxXHU4RDQ0XHU2RTkwXHU2MjUzXHU1MzA1XHU1MjMwZGlzdFx1NEUwQlx1NzY4NFx1NEUwRFx1NTQwQ1x1NzZFRVx1NUY1NVxyXG4gICAgICByb2xsdXBPcHRpb25zOiB7XHJcbiAgICAgICAgb3V0cHV0OiB7XHJcbiAgICAgICAgICBjaHVua0ZpbGVOYW1lczogJ3N0YXRpYy9qcy9bbmFtZV0tW2hhc2hdLmpzJyxcclxuICAgICAgICAgIGVudHJ5RmlsZU5hbWVzOiAnc3RhdGljL2pzL1tuYW1lXS1baGFzaF0uanMnLFxyXG4gICAgICAgICAgYXNzZXRGaWxlTmFtZXM6ICdzdGF0aWMvW2V4dF0vW25hbWVdLVtoYXNoXS5bZXh0XSdcclxuICAgICAgICB9XHJcbiAgICAgIH1cclxuICAgIH1cclxuICB9XHJcbn0pXHJcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBbVEsU0FBUyxLQUFLLHFCQUFx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
