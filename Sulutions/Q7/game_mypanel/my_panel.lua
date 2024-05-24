local MyPanel = {}
-- Initialization function
function init()
  -- Connect to game start and end events
  connect(g_game, { onGameStart = MyPanel.online,
                    onGameEnd   = MyPanel.offline })
  -- Import custom UI styles
  g_ui.importStyle('modules/game_mypanel/my_panel.otui')
  -- Display the custom window
  MyPanel.customWindow = g_ui.displayUI('my_panel')
  MyPanel.customWindow:show()
  -- Get references to the panel and button
  MyPanel.customPanelWidget = MyPanel.customWindow:getChildById('MyWidget')
  MyPanel.customPanelButton = MyPanel.customPanelWidget:getChildById('jumpButton')
  MyPanel.customPanelButton:show()

  -- MyPanel.startButtonAnimation() not working
  MyPanel.startButtonAnimation()
end
-- Termination function
function terminate()
  disconnect(g_game, { onGameStart = MyPanel.online,
                       onGameEnd   = MyPanel.offline })
  -- Destroy the custom window
  MyPanel.customWindow:destroy()
  MyPanel = nil
end

function MyPanel.onButtonClick()
  local button = MyPanel.customPanelButton
  button:hide()

  local newX = math.random(0, MyPanel.customPanelWidget:getWidth() - button:getWidth())
  local newY = math.random(0, MyPanel.customPanelWidget:getHeight() - button:getHeight())

  button:setMarginTop(newY)
  button:setMarginLeft(newX)

  scheduleEvent(function()
    button:show()
  end, 500)
end


function MyPanel.online()
  MyPanel.toggle()
end

function MyPanel.offline()
  MyPanel.customWindow:hide()
end
-- Function to start button animation
function MyPanel.startButtonAnimation()
  local button = MyPanel.customPanelButton
  local startX = 10
  local endX = MyPanel.customWindow:getWidth() - button:getWidth() - 10
  local step = 2
  -- Recursive function to move the button
  local function moveButton()
     -- If the button reaches the end, reset its position
    if button:getX() >= endX then
      button:setX(startX)
    else
      button:setX(button:getX() + step)
    end
    -- Move the button every 50 milliseconds
    scheduleEvent(moveButton, 50)
  end
  
  moveButton()
end

return MyPanel