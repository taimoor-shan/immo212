# 📝 Custom System Message User Guide

## How to Input Custom Translation Messages (Step-by-Step)

This guide shows exactly how end users can easily configure custom system messages for ChatGPT translations through the admin interface.

---

## 🚀 **Quick Access Path**

**Navigation:** Admin Dashboard → Settings → Others → Smart Auto Translations Pro

**Direct URL:** `/admin/settings/others/vig-auto-translations`

---

## 📋 **Step-by-Step Process**

### Step 1: Navigate to Plugin Settings

1. **Login** to your Botble CMS admin dashboard
2. **Go to** Settings (⚙️ icon in main menu)
3. **Click** "Others" tab
4. **Select** "Smart Auto Translations Pro"

### Step 2: Configure Translation Provider

1. **Select** "ChatGPT Translate" radio button
2. This will reveal the ChatGPT configuration section

### Step 3: Configure ChatGPT Model

1. **API Key Field**: Enter your OpenAI API key
2. **Model Selection Dropdown**: Choose your preferred model:
   - `GPT-4.1` (Latest Flagship) - Best quality, 1M token context
   - `GPT-4.1 Mini` - Balanced speed/cost, 128K tokens  
   - `GPT-4.1 Nano` - Fastest, lowest cost, 32K tokens
   - Legacy models available for backward compatibility

### Step 4: Input Custom System Message

**Field Location**: Large textarea under "Custom System Message"

**What You'll See:**
```
┌─────────────────────────────────────────────────┐
│ Custom System Message                           │
│ ┌─────────────────────────────────────────────┐ │
│ │ You are an expert professional translator   │ │
│ │ with specialized expertise in               │ │
│ │ {source_language} to {target_language}      │ │
│ │ translations. Your task is to provide       │ │
│ │ accurate, contextually appropriate...       │ │
│ │                                             │ │
│ │ [Large text area - 6 rows tall]            │ │
│ └─────────────────────────────────────────────┘ │
│                                                 │
│ 💡 Optional: Customize the system message to   │
│ define translation style and requirements.      │
│ Use placeholders: {source_language},           │
│ {target_language}, {source}, {target}.         │
│ Leave empty to use the default professional    │
│ translation prompt.                             │
│                                                 │
│ Characters: 0/2000                              │
└─────────────────────────────────────────────────┘
```

---

## ✏️ **How to Create Custom Messages**

### Template with Placeholders

**Available Placeholders:**
- `{source_language}` → "English", "Spanish", etc.
- `{target_language}` → "Spanish", "French", etc.  
- `{source}` → "en", "es", etc. (language codes)
- `{target}` → "es", "fr", etc. (language codes)

### Pre-Built Examples You Can Copy & Paste

#### 🛒 **E-commerce Websites**
```
You are a professional translator specializing in {source_language} to {target_language} translations for e-commerce websites. 

REQUIREMENTS:
• Maintain a friendly, commercial tone
• Use terminology appropriate for online shopping
• Keep all product names and brand references unchanged
• Adapt pricing and currency formats to {target_language} standards
• Use persuasive language that encourages purchases
• Preserve all HTML formatting and variables exactly

Return only the translated text with no explanations.
```

#### 💼 **Corporate/Business Content**
```
You are a business translator expert in {source_language} to {target_language} corporate communications.

STYLE GUIDE:
• Use formal, professional language
• Maintain corporate tone and terminology
• Preserve all technical terms and acronyms
• Keep company names and proper nouns unchanged
• Use industry-standard business terminology
• Maintain document formatting exactly

Output only the translation without commentary.
```

#### 🔧 **Technical Documentation**
```
You are a technical translator specializing in {source_language} to {target_language} software documentation.

TECHNICAL RULES:
• Preserve all code snippets, file names, and technical terms
• Keep English technical terms if commonly used in {target_language}
• Maintain precise technical accuracy
• Use formal academic language
• Keep all formatting, indentation, and structure identical
• Preserve all variables: {{var}}, :param, %config% exactly

Return only translated content.
```

#### 🎨 **Marketing & Creative Content**
```
You are a creative marketing translator from {source_language} to {target_language}.

CREATIVE APPROACH:
• Adapt tone to be engaging and culturally appropriate
• Maintain the original message's emotional impact
• Localize cultural references where suitable
• Use persuasive, compelling language
• Keep brand names and slogans unchanged
• Preserve marketing formatting and emphasis

Provide only the translated marketing copy.
```

#### 🏥 **Healthcare/Medical**
```
You are a medical translator specialized in {source_language} to {target_language} healthcare content.

MEDICAL STANDARDS:
• Use precise medical terminology in {target_language}
• Maintain clinical accuracy above all
• Keep medication names in their international forms
• Use formal medical language appropriate for professionals
• Preserve all medical formatting and structures
• Keep dosage and measurement units unchanged

Return only the medical translation.
```

---

## 🎯 **Smart Features Built Into the Interface**

### 1. **Live Character Counter**
- Shows `0/2000` characters as you type
- Prevents exceeding the 2000 character limit
- Real-time feedback while typing

### 2. **Helpful Placeholder Text**
- Default template automatically appears in the field
- Shows the enhanced GPT-4.1 optimized prompt
- Easy to clear and replace with your custom message

### 3. **Validation & Error Handling**
- Form validates your message before saving
- Shows helpful error messages if format is incorrect
- Prevents saving invalid configurations

### 4. **Contextual Help Text**
- Always visible help text explains placeholders
- Examples show how to use language variables
- Clear guidance on formatting requirements

---

## 💡 **Pro Tips for Creating Effective Custom Messages**

### ✅ **Best Practices**

1. **Be Specific**: The more detailed your instructions, the better GPT-4.1 performs
2. **Use Placeholders**: Always use `{source_language}` and `{target_language}` for dynamic content
3. **Set Clear Rules**: Number your requirements (1. 2. 3.) for GPT-4.1's superior instruction following
4. **Include Output Format**: Always specify "Return only the translated text"
5. **Test Thoroughly**: Use the test script to verify your custom prompts work well

### ❌ **Common Mistakes to Avoid**

1. **Don't** make messages too long (2000 char limit)
2. **Don't** forget to use placeholders for language names
3. **Don't** include conflicting instructions
4. **Don't** ask for explanations in the output (GPT-4.1 follows this literally)
5. **Don't** forget to preserve formatting requirements

---

## 🧪 **Testing Your Custom Messages**

After saving your custom system message:

```bash
# Test your custom message
php test-providers.php
```

The test script will show:
- ✅ Configuration status
- 📝 Your custom message length and content
- 🧪 Actual translation test results
- ⏱️ Performance metrics

---

## 📱 **Mobile-Friendly Interface**

The admin interface is fully responsive:
- **Large touchscreen-friendly** text areas
- **Clear labels** and help text
- **Easy navigation** on tablets and phones
- **Auto-save** prevents data loss

---

## 🔄 **Easy Updates and Changes**

### To Modify Your Message:
1. Navigate back to the same settings page
2. Your current message will be preserved in the textarea
3. Edit as needed
4. Click "Save Settings"
5. Test immediately with `php test-providers.php`

### To Reset to Default:
1. Clear the textarea completely (delete all text)
2. Save settings
3. The system will automatically use the enhanced GPT-4.1 default

---

## 🎉 **That's It!**

The process is designed to be **intuitive and user-friendly**:
- **Visual** textarea with helpful placeholders
- **Clear** instructions and examples
- **Instant** validation and feedback
- **Easy** testing and verification

Your custom system messages will immediately improve translation quality for your specific use case! 🚀
