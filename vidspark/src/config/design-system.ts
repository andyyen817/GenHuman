/**
 * Vidspark設計系統配置
 * 基於HeyGen風格和靜態頁面設計的統一設計規範
 */

// 顏色系統
export const colors = {
  // 靜態頁面主色調
  static: {
    primary: {
      50: '#eff6ff',
      100: '#dbeafe',
      200: '#bfdbfe',
      300: '#93c5fd',
      400: '#60a5fa',
      500: '#3b82f6', // 淺藍色
      600: '#2563eb', // 主要藍色
      700: '#1d4ed8',
      800: '#1e40af', // 深藍色
      900: '#1e3a8a',
    },
    secondary: {
      purple: '#8b5cf6',
      green: '#10b981',
      orange: '#f59e0b',
    },
  },

  // 控制台風格配色
  console: {
    dark: {
      background: '#0f172a', // slate-900
      card: '#1e293b',       // slate-800
      border: '#334155',     // slate-700
      text: {
        primary: '#f8fafc',   // slate-50
        secondary: '#94a3b8', // slate-400
      },
      accent: '#3b82f6',     // 藍色
      success: '#22c55e',    // 綠色
    },
    light: {
      background: '#ffffff',
      card: '#f8fafc',
      border: '#e2e8f0',
      text: {
        primary: '#0f172a',
        secondary: '#64748b',
      },
      accent: '#3b82f6',
      success: '#22c55e',
    },
  },

  // 主色調 - 向後兼容
  primary: {
    50: '#f0f9ff',
    100: '#e0f2fe',
    200: '#bae6fd',
    300: '#7dd3fc',
    400: '#38bdf8',
    500: '#0ea5e9', // 主色
    600: '#0284c7',
    700: '#0369a1',
    800: '#075985',
    900: '#0c4a6e'
  },
  
  // 輔助色
  secondary: {
    50: '#faf5ff',
    100: '#f3e8ff',
    200: '#e9d5ff',
    300: '#d8b4fe',
    400: '#c084fc',
    500: '#a855f7',
    600: '#9333ea',
    700: '#7c3aed',
    800: '#6b21a8',
    900: '#581c87'
  },
  
  // 狀態色（通用）
  status: {
    success: '#22c55e', // 綠色
    warning: '#f59e0b', // 黃色
    error: '#ef4444',   // 紅色
    info: '#3b82f6',    // 藍色
  },

  // 詳細狀態色（向後兼容）
  success: {
    50: '#f0fdf4',
    100: '#dcfce7',
    200: '#bbf7d0',
    300: '#86efac',
    400: '#4ade80',
    500: '#22c55e',
    600: '#16a34a',
    700: '#15803d',
    800: '#166534',
    900: '#14532d'
  },
  
  warning: {
    50: '#fffbeb',
    100: '#fef3c7',
    200: '#fde68a',
    300: '#fcd34d',
    400: '#fbbf24',
    500: '#f59e0b',
    600: '#d97706',
    700: '#b45309',
    800: '#92400e',
    900: '#78350f'
  },
  
  error: {
    50: '#fef2f2',
    100: '#fee2e2',
    200: '#fecaca',
    300: '#fca5a5',
    400: '#f87171',
    500: '#ef4444',
    600: '#dc2626',
    700: '#b91c1c',
    800: '#991b1b',
    900: '#7f1d1d'
  },
  
  // 中性色
  gray: {
    50: '#f9fafb',
    100: '#f3f4f6',
    200: '#e5e7eb',
    300: '#d1d5db',
    400: '#9ca3af',
    500: '#6b7280',
    600: '#4b5563',
    700: '#374151',
    800: '#1f2937',
    900: '#111827'
  },
  
  // 特殊色
  white: '#ffffff',
  black: '#000000',
  transparent: 'transparent'
};

// 字體系統
export const typography = {
  fontFamily: {
    sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
    mono: ['JetBrains Mono', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace']
  },
  
  fontSize: {
    xs: '0.75rem',     // 12px
    sm: '0.875rem',    // 14px
    base: '1rem',      // 16px
    lg: '1.125rem',    // 18px
    xl: '1.25rem',     // 20px
    '2xl': '1.5rem',   // 24px
    '3xl': '1.875rem', // 30px
    '4xl': '2.25rem',  // 36px
    '5xl': '3rem',     // 48px
    '6xl': '3.75rem'   // 60px
  },
  
  fontWeight: {
    thin: '100',
    extralight: '200',
    light: '300',
    normal: '400',
    medium: '500',
    semibold: '600',
    bold: '700',
    extrabold: '800',
    black: '900'
  },
  
  lineHeight: {
    none: '1',
    tight: '1.25',
    snug: '1.375',
    normal: '1.5',
    relaxed: '1.625',
    loose: '2'
  },
  
  letterSpacing: {
    tighter: '-0.05em',
    tight: '-0.025em',
    normal: '0em',
    wide: '0.025em',
    wider: '0.05em',
    widest: '0.1em'
  }
};

// 間距系統
export const spacing = {
  0: '0px',
  1: '0.25rem',   // 4px
  2: '0.5rem',    // 8px
  3: '0.75rem',   // 12px
  4: '1rem',      // 16px
  5: '1.25rem',   // 20px
  6: '1.5rem',    // 24px
  7: '1.75rem',   // 28px
  8: '2rem',      // 32px
  9: '2.25rem',   // 36px
  10: '2.5rem',   // 40px
  11: '2.75rem',  // 44px
  12: '3rem',     // 48px
  14: '3.5rem',   // 56px
  16: '4rem',     // 64px
  20: '5rem',     // 80px
  24: '6rem',     // 96px
  28: '7rem',     // 112px
  32: '8rem',     // 128px
  36: '9rem',     // 144px
  40: '10rem',    // 160px
  44: '11rem',    // 176px
  48: '12rem',    // 192px
  52: '13rem',    // 208px
  56: '14rem',    // 224px
  60: '15rem',    // 240px
  64: '16rem',    // 256px
  72: '18rem',    // 288px
  80: '20rem',    // 320px
  96: '24rem'     // 384px
};

// 圓角系統
export const borderRadius = {
  none: '0px',
  sm: '0.125rem',   // 2px
  base: '0.25rem',  // 4px
  md: '0.375rem',   // 6px
  lg: '0.5rem',     // 8px
  xl: '0.75rem',    // 12px
  '2xl': '1rem',    // 16px
  '3xl': '1.5rem',  // 24px
  full: '9999px'
};

// 陰影系統
export const boxShadow = {
  sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
  base: '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
  md: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
  lg: '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
  xl: '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
  '2xl': '0 25px 50px -12px rgb(0 0 0 / 0.25)',
  inner: 'inset 0 2px 4px 0 rgb(0 0 0 / 0.05)',
  none: '0 0 #0000'
};

// 響應式斷點
export const breakpoints = {
  sm: '640px',   // 手機橫屏
  md: '768px',   // 平板
  lg: '1024px',  // 小桌面
  xl: '1280px',  // 桌面
  '2xl': '1536px' // 大桌面
};

// Z-index層級
export const zIndex = {
  auto: 'auto',
  0: '0',
  10: '10',
  20: '20',
  30: '30',
  40: '40',
  50: '50',
  dropdown: '1000',
  sticky: '1020',
  fixed: '1030',
  modal: '1040',
  popover: '1050',
  tooltip: '1060',
  toast: '1070'
};

// 動畫系統
export const animation = {
  duration: {
    75: '75ms',
    100: '100ms',
    150: '150ms',
    200: '200ms',
    300: '300ms',
    500: '500ms',
    700: '700ms',
    1000: '1000ms'
  },
  
  timing: {
    linear: 'linear',
    ease: 'ease',
    easeIn: 'ease-in',
    easeOut: 'ease-out',
    easeInOut: 'ease-in-out'
  },
  
  keyframes: {
    fadeIn: {
      '0%': { opacity: '0' },
      '100%': { opacity: '1' }
    },
    fadeOut: {
      '0%': { opacity: '1' },
      '100%': { opacity: '0' }
    },
    slideInUp: {
      '0%': { transform: 'translateY(100%)', opacity: '0' },
      '100%': { transform: 'translateY(0)', opacity: '1' }
    },
    slideInDown: {
      '0%': { transform: 'translateY(-100%)', opacity: '0' },
      '100%': { transform: 'translateY(0)', opacity: '1' }
    },
    slideInLeft: {
      '0%': { transform: 'translateX(-100%)', opacity: '0' },
      '100%': { transform: 'translateX(0)', opacity: '1' }
    },
    slideInRight: {
      '0%': { transform: 'translateX(100%)', opacity: '0' },
      '100%': { transform: 'translateX(0)', opacity: '1' }
    },
    bounce: {
      '0%, 20%, 53%, 80%, 100%': { transform: 'translate3d(0,0,0)' },
      '40%, 43%': { transform: 'translate3d(0, -30px, 0)' },
      '70%': { transform: 'translate3d(0, -15px, 0)' },
      '90%': { transform: 'translate3d(0, -4px, 0)' }
    }
  }
};

// 組件樣式預設
export const components = {
  button: {
    base: {
      display: 'inline-flex',
      alignItems: 'center',
      justifyContent: 'center',
      borderRadius: borderRadius.md,
      fontSize: typography.fontSize.sm,
      fontWeight: typography.fontWeight.medium,
      lineHeight: typography.lineHeight.none,
      transition: 'all 150ms ease-in-out',
      cursor: 'pointer',
      userSelect: 'none',
      outline: 'none',
      border: 'none'
    },
    
    sizes: {
      xs: {
        padding: `${spacing[1]} ${spacing[2]}`,
        fontSize: typography.fontSize.xs
      },
      sm: {
        padding: `${spacing[2]} ${spacing[3]}`,
        fontSize: typography.fontSize.sm
      },
      md: {
        padding: `${spacing[3]} ${spacing[4]}`,
        fontSize: typography.fontSize.base
      },
      lg: {
        padding: `${spacing[4]} ${spacing[6]}`,
        fontSize: typography.fontSize.lg
      },
      xl: {
        padding: `${spacing[4]} ${spacing[8]}`,
        fontSize: typography.fontSize.xl
      }
    },
    
    variants: {
      primary: {
        backgroundColor: colors.primary[500],
        color: colors.white,
        '&:hover': {
          backgroundColor: colors.primary[600]
        },
        '&:active': {
          backgroundColor: colors.primary[700]
        },
        '&:disabled': {
          backgroundColor: colors.gray[300],
          cursor: 'not-allowed'
        }
      },
      secondary: {
        backgroundColor: colors.gray[100],
        color: colors.gray[900],
        '&:hover': {
          backgroundColor: colors.gray[200]
        },
        '&:active': {
          backgroundColor: colors.gray[300]
        }
      },
      outline: {
        backgroundColor: 'transparent',
        color: colors.primary[500],
        border: `1px solid ${colors.primary[500]}`,
        '&:hover': {
          backgroundColor: colors.primary[50]
        }
      },
      ghost: {
        backgroundColor: 'transparent',
        color: colors.gray[600],
        '&:hover': {
          backgroundColor: colors.gray[100],
          color: colors.gray[900]
        }
      },
      danger: {
        backgroundColor: colors.error[500],
        color: colors.white,
        '&:hover': {
          backgroundColor: colors.error[600]
        }
      }
    }
  },
  
  input: {
    base: {
      display: 'block',
      width: '100%',
      padding: `${spacing[3]} ${spacing[4]}`,
      fontSize: typography.fontSize.base,
      lineHeight: typography.lineHeight.normal,
      color: colors.gray[900],
      backgroundColor: colors.white,
      border: `1px solid ${colors.gray[300]}`,
      borderRadius: borderRadius.md,
      outline: 'none',
      transition: 'border-color 150ms ease-in-out, box-shadow 150ms ease-in-out',
      '&:focus': {
        borderColor: colors.primary[500],
        boxShadow: `0 0 0 3px ${colors.primary[100]}`
      },
      '&:disabled': {
        backgroundColor: colors.gray[50],
        color: colors.gray[500],
        cursor: 'not-allowed'
      },
      '&::placeholder': {
        color: colors.gray[400]
      }
    },
    
    sizes: {
      sm: {
        padding: `${spacing[2]} ${spacing[3]}`,
        fontSize: typography.fontSize.sm
      },
      md: {
        padding: `${spacing[3]} ${spacing[4]}`,
        fontSize: typography.fontSize.base
      },
      lg: {
        padding: `${spacing[4]} ${spacing[5]}`,
        fontSize: typography.fontSize.lg
      }
    },
    
    states: {
      error: {
        borderColor: colors.error[500],
        '&:focus': {
          borderColor: colors.error[500],
          boxShadow: `0 0 0 3px ${colors.error[100]}`
        }
      },
      success: {
        borderColor: colors.success[500],
        '&:focus': {
          borderColor: colors.success[500],
          boxShadow: `0 0 0 3px ${colors.success[100]}`
        }
      }
    }
  },
  
  card: {
    base: {
      backgroundColor: colors.white,
      borderRadius: borderRadius.lg,
      boxShadow: boxShadow.base,
      overflow: 'hidden'
    },
    
    variants: {
      elevated: {
        boxShadow: boxShadow.lg
      },
      outlined: {
        border: `1px solid ${colors.gray[200]}`,
        boxShadow: 'none'
      },
      flat: {
        boxShadow: 'none'
      }
    }
  },
  
  modal: {
    overlay: {
      position: 'fixed',
      top: '0',
      left: '0',
      right: '0',
      bottom: '0',
      backgroundColor: 'rgba(0, 0, 0, 0.5)',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      zIndex: zIndex.modal
    },
    
    content: {
      backgroundColor: colors.white,
      borderRadius: borderRadius.lg,
      boxShadow: boxShadow.xl,
      maxWidth: '90vw',
      maxHeight: '90vh',
      overflow: 'auto'
    }
  }
};

// 主題配置
export const themes = {
  light: {
    colors: {
      background: colors.white,
      surface: colors.gray[50],
      primary: colors.primary[500],
      secondary: colors.secondary[500],
      text: {
        primary: colors.gray[900],
        secondary: colors.gray[600],
        disabled: colors.gray[400]
      },
      border: colors.gray[200],
      divider: colors.gray[100]
    }
  },
  
  dark: {
    colors: {
      background: colors.gray[900],
      surface: colors.gray[800],
      primary: colors.primary[400],
      secondary: colors.secondary[400],
      text: {
        primary: colors.white,
        secondary: colors.gray[300],
        disabled: colors.gray[500]
      },
      border: colors.gray[700],
      divider: colors.gray[800]
    }
  }
};

// 導出完整的設計系統
export const designSystem = {
  colors,
  typography,
  spacing,
  borderRadius,
  boxShadow,
  breakpoints,
  zIndex,
  animation,
  components,
  themes
};

export default designSystem;