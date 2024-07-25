# Tic Tac Toe Game Design System: CSS and Styling

## Colour Palette

The game uses a colour scheme defined in CSS variables:

- `--colour-nav`: #52b8f3 (Light blue for navigation)
- `--colour-background`: #c8e0ee (Soft blue for page background)
- `--colour-x`: #5700c1 (Deep purple for X player)
- `--colour-o`: #ff0040 (Bright red for O player)
- `--colour-board-background`: #ffffff (White for game board)
- `--colour-winner`: #24ff71 (Bright green for winning moves)
- `--cta-colour`: #66b0db (Blue for call-to-action elements)
- `--cta-hover-colour`: #4285ad (Darker blue for CTA hover states)

## Typography

- Primary font: Arial (with sans-serif fallback)
- Font sizes are mostly relative (em units) for better scalability

## Layout

### Global Layout (`global.css`)

- Uses a CSS reset for consistent styling across browsers
- Employs flexbox for centering and alignment
- Responsive design with `viewport` meta tag

### Game Board (`styles.css`)

- Grid layout for the 3x3 game board
- Responsive sizing using viewport units (vw, vh)

### Admin Panel (`admin.css`)

- Grid layout for displaying multiple game states
- Card-based design for individual game information

### Login Page (`login.css`)

- Centered layout with flexbox
- Card-like container for the login form

## Components

### Navigation

- Consistent styling across pages
- Hover effects on navigation items

### Buttons

- Clear, clickable styling with hover effects
- Consistent use of `--cta-colour` and `--cta-hover-colour`

### Game Cells

- Square cells with responsive sizing
- Clear borders for grid visibility
- Hover effects for interactive feedback

### Forms

- Consistent styling for input fields and submit buttons
- Clear visual feedback for form interactions

## Responsive Design

- Media queries adjust layout and sizing for different screen sizes
- Flexible layouts using percentage and viewport units
- Hover and focus states for interactive elements

## CSS Organization

1. `global.css`: Base styles and variables
2. `styles.css`: Game-specific styles
3. `admin.css`: Admin panel styles
4. `login.css`: Login page styles

This separation allows for easy maintenance and potential theme customization.

## Best Practices

- Use of CSS variables for easy theme adjustments
- Consistent naming conventions (e.g., BEM-like class names)
- Minimizing use of !important
- Grouping related styles for improved readability

## Future Enhancements

- Implement a CSS preprocessor (e.g., SASS) for more advanced styling features
- Create a dark mode theme
- Further improve responsiveness for a wider range of devices
- Enhance animations for game interactions

This design system ensures a consistent, attractive, and user-friendly interface across the Tic Tac Toe game application.
