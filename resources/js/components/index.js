import Vue from 'vue'
import Card from './Card'
import Child from './Child'
import Button from './Button'
import { HasError, AlertError, AlertSuccess } from 'vform'

// Components that are registered globaly.
[
  Card,
  Child,
  Button,
  HasError,
  AlertError,
  AlertSuccess
].forEach(Component => {
  Vue.component(Component.name, Component)
})
