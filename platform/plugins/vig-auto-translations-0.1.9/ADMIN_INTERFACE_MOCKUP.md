# 🖥️ Admin Interface Visual Mockup

## Exact Visual Layout Users Will See

This shows the precise admin interface layout for inputting custom system messages.

---

## 📱 **Full Admin Settings Page Layout**

```
┌─────────────────────────────────────────────────────────────────────┐
│  🏠 Botble CMS Admin Dashboard                             [Profile] │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  📊 Dashboard  👥 Users  📰 Blog  🏠 Real Estate  ⚙️ Settings       │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ⚙️ Settings                                                        │
│                                                                     │
│  📋 General    🔐 Security    📧 Email    📱 Others                  │
│                                           ▲ Selected                │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  📱 Others Settings                                                 │
│                                                                     │
│  🌍 Smart Auto Translations Pro    [Configure] ←── Click Here      │
│  🔧 Other Plugin Settings...                                       │
│  📊 Analytics Settings...                                          │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 **Smart Auto Translations Pro Configuration Page**

```
┌─────────────────────────────────────────────────────────────────────┐
│  🌍 Smart Auto Translations Pro Settings                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  🔧 Translation Provider                                            │
│                                                                     │
│  ○ Google Translate        ○ Amazon Translate      ● ChatGPT       │
│                                                     ▲ Selected      │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  🤖 ChatGPT/OpenAI Configuration                                   │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │                                                                 │ │
│  │  🔑 API Key *                                                   │ │
│  │  ┌─────────────────────────────────────────────────────────────┐ │ │
│  │  │ sk-proj-abcd1234...                                         │ │ │
│  │  └─────────────────────────────────────────────────────────────┘ │ │
│  │                                                                 │ │
│  │  🎯 Model Selection                                             │ │
│  │  ┌─────────────────────────────────────────────────────────────┐ │ │
│  │  │ GPT-4.1 (Latest Flagship) - Superior coding, 1M tokens ▼   │ │ │
│  │  └─────────────────────────────────────────────────────────────┘ │ │
│  │     Options:                                                    │ │
│  │     • GPT-4.1 - Best quality, 1M context                      │ │
│  │     • GPT-4.1 Mini - Balanced speed/cost                      │ │
│  │     • GPT-4.1 Nano - Fastest, lowest cost                     │ │
│  │                                                                 │ │
│  │  📝 Custom System Message                                      │ │
│  │  ┌─────────────────────────────────────────────────────────────┐ │ │
│  │  │ You are an expert professional translator with specialized  │ │ │
│  │  │ expertise in {source_language} to {target_language}        │ │ │
│  │  │ translations. Your task is to provide accurate,            │ │ │
│  │  │ contextually appropriate translations that maintain the     │ │ │
│  │  │ exact intent and nuance of the original text.              │ │ │
│  │  │                                                             │ │ │
│  │  │ CRITICAL TRANSLATION RULES (follow exactly):               │ │ │
│  │  │ 1. OUTPUT FORMAT: Return ONLY the translated text...       │ │ │
│  │  │                                                             │ │ │
│  │  │ [Large text area continues - 6 rows total]                 │ │ │
│  │  └─────────────────────────────────────────────────────────────┘ │ │
│  │                                                                 │ │
│  │  💡 Optional: Customize the system message to define           │ │
│  │  translation style and requirements. Use placeholders:         │ │
│  │  {source_language}, {target_language}, {source}, {target}.    │ │
│  │  Leave empty to use the default professional prompt.           │ │
│  │                                                                 │ │
│  │  Characters: 1,247 / 2,000                                     │ │
│  │                                                                 │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  [💾 Save Settings]                                   [🧪 Test]     │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎨 **Interactive Features Users Will Experience**

### 1. **Model Dropdown Opens Like This:**

```
┌─────────────────────────────────────────────────────────────────────┐
│  🎯 Model Selection                                                 │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │ ✅ GPT-4.1 (Latest Flagship) - Superior coding, 1M tokens      │ │ ← Selected
│  │ ○  GPT-4.1 Mini - Balanced speed/cost, 128K tokens            │ │
│  │ ○  GPT-4.1 Nano - Ultra-low latency, 32K tokens               │ │
│  │ ──────────────────────────────────────────────────────────────── │ │
│  │ ○  GPT-4o (Legacy) - Previous flagship                        │ │
│  │ ○  GPT-4 Turbo (Legacy) - Older model                         │ │
│  │ ○  GPT-3.5 Turbo (Budget) - Most cost-effective              │ │
│  └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

### 2. **Character Counter Updates Live:**

```
┌─────────────────────────────────────────────────────────────────────┐
│  📝 Custom System Message                                          │
│  ┌─────────────────────────────────────────────────────────────────┐ │
│  │ You are a professional e-commerce translator...                │ │
│  │ [User is typing here]█                                         │ │
│  │                                                                 │ │
│  └─────────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Characters: 247 / 2,000  🟢 (Green when under limit)              │
│  Characters: 1,847 / 2,000  🟡 (Yellow when approaching limit)     │
│  Characters: 2,000 / 2,000  🔴 (Red when at limit)                 │
└─────────────────────────────────────────────────────────────────────┘
```

### 3. **Real-Time Validation Messages:**

```
┌─────────────────────────────────────────────────────────────────────┐
│  ✅ Configuration Valid                                             │
│  • API key format is correct                                       │
│  • Model selection is valid                                        │
│  • Custom system message within limits                             │
│                                                                     │
│  [💾 Save Settings]  [🧪 Test Configuration]                       │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📱 **Mobile/Tablet View**

```
┌─────────────────────────────────┐
│ 🌍 Smart Auto Translations Pro │
├─────────────────────────────────┤
│                                 │
│ 🔧 Provider                     │
│ ○ Google  ○ AWS  ● ChatGPT     │
│                                 │
│ 🤖 ChatGPT Config               │
│ ┌─────────────────────────────┐ │
│ │ 🔑 API Key                  │ │
│ │ sk-proj-abc123...           │ │
│ │                             │ │
│ │ 🎯 Model                    │ │
│ │ GPT-4.1 (Latest) ▼         │ │
│ │                             │ │
│ │ 📝 Custom Message           │ │
│ │ ┌─────────────────────────┐ │ │
│ │ │ You are an expert       │ │ │
│ │ │ professional translator │ │ │
│ │ │ specializing in...      │ │ │
│ │ │                         │ │ │
│ │ │ [Large touch-friendly   │ │ │
│ │ │  text area]             │ │ │
│ │ └─────────────────────────┘ │ │
│ │                             │ │
│ │ 💡 Use placeholders:        │ │
│ │ {source_language},          │ │
│ │ {target_language}           │ │
│ │                             │ │
│ │ Chars: 342/2000             │ │
│ └─────────────────────────────┘ │
│                                 │
│ [💾 Save]     [🧪 Test]        │
│                                 │
└─────────────────────────────────┘
```

---

## 🎉 **User Experience Highlights**

### **Super Easy Process:**

1. **One Click Navigation**: Settings → Others → Smart Auto Translations Pro
2. **Visual Selection**: Click ChatGPT radio button to reveal options
3. **Smart Defaults**: Default message already populated as placeholder
4. **Copy & Paste Ready**: Pre-built examples in documentation
5. **Instant Feedback**: Live character counter and validation
6. **Test Before Use**: Built-in test functionality

### **User-Friendly Features:**

- ✅ **Large Text Area**: Easy to read and edit (6 rows tall)
- ✅ **Helpful Placeholders**: Shows exactly what to expect
- ✅ **Clear Instructions**: Always-visible help text
- ✅ **Real-time Validation**: Immediate feedback on input
- ✅ **Mobile Optimized**: Works perfectly on all devices
- ✅ **Auto-save**: Prevents accidental data loss
- ✅ **Copy/Paste Friendly**: Easy to use pre-built examples

### **Professional Interface:**

- 🎨 **Modern Design**: Consistent with Botble CMS styling
- 🔧 **Intuitive Layout**: Logical flow from basic to advanced settings
- 📱 **Responsive**: Adapts beautifully to any screen size
- ⚡ **Fast Loading**: Lightweight and optimized interface
- 🛡️ **Secure**: Proper validation and sanitization

This interface makes it **incredibly easy** for users to create and manage custom system messages, even if they have no technical background! 🚀
