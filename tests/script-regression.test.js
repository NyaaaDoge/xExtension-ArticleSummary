const assert = require('node:assert/strict');
const fs = require('node:fs');
const test = require('node:test');
const vm = require('node:vm');

function createElement(classNames, dataset = {}) {
  const element = {
    classNames: new Set(classNames.split(/\s+/).filter(Boolean)),
    dataset,
    disabled: false,
    innerHTML: '',
    parentNode: null,
    children: [],
    classList: {
      add(name) {
        this.owner.classNames.add(name);
      },
      remove(name) {
        this.owner.classNames.delete(name);
      },
      contains(name) {
        return this.owner.classNames.has(name);
      },
    },
    appendChild(child) {
      child.parentNode = this;
      this.children.push(child);
      return child;
    },
    matches(selector) {
      const className = selector.startsWith('.') ? selector.slice(1) : selector;
      return this.classNames.has(className);
    },
    querySelector(selector) {
      const className = selector.startsWith('.') ? selector.slice(1) : selector;
      const stack = [...this.children];
      while (stack.length > 0) {
        const current = stack.shift();
        if (current.classNames.has(className)) {
          return current;
        }
        stack.push(...current.children);
      }

      return null;
    },
  };

  element.classList.owner = element;

  return element;
}

function loadScript() {
  const script = fs.readFileSync('static/script.js', 'utf8');
  const context = {
    console: {
      log() {},
      error() {},
    },
    __duringPost: null,
    context: { csrf: 'csrf-token' },
    axios: {
      async post() {
        if (context.__duringPost) {
          context.__duringPost();
        }

        return {
          status: 200,
          data: {
            response: {
              data: 'Backend error',
              error: true,
            },
          },
        };
      },
    },
    document: {
      readyState: 'loading',
      addEventListener() {},
      getElementById() {
        return {
          addEventListener() {},
        };
      },
    },
  };

  vm.createContext(context);
  vm.runInContext(script, context);
  return context;
}

test('summary click resolves the outer summary wrapper when button is inside header', async () => {
  const context = loadScript();
  const wrapper = createElement('oai-summary-wrap');
  const header = wrapper.appendChild(createElement('oai-summary-header'));
  const button = header.appendChild(createElement('btn btn-important oai-summary-btn', {
    loadingText: 'Loading...',
    request: '/summary',
  }));
  const content = wrapper.appendChild(createElement('oai-summary-content'));
  let sawLoadingBeforeBackendResponse = false;

  context.__duringPost = () => {
    assert.equal(content.innerHTML, 'Loading...');
    assert.equal(button.disabled, true);
    assert.equal(wrapper.classList.contains('oai-loading'), true);
    sawLoadingBeforeBackendResponse = true;
  };

  await context.summarizeButtonClick(button);

  assert.equal(sawLoadingBeforeBackendResponse, true);
});
